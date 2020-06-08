<?php

namespace app\api\controller\wxapp;

use app\api\library\Service;
use app\api\model\aurora\Third;
use app\common\library\Auth;
use app\common\library\Sms;
use fast\Http;
use think\Config;
use think\Db;
use think\Exception;
use think\Validate;

/**
 * 会员
 */
class Customer extends Base
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['login', 'syncInfo'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    protected $token = '';

    public function _initialize()
    {
        $this->token = $this->request->post('token');

        if ($this->request->action() == 'login' && $this->token) {
            $this->request->post(['token' => '']);
        }

        parent::_initialize();
    }

    /**
     * 微信小程序登录
     */
    public function login()
    {
        $code = $this->request->post("code");

        if (!$code) {
            $this->error("参数不正确");
        }

        //获取微信小程序配置信息
        $site = Config::get('site');

        $params = [
            'appid' => $site['wxapp_appid'],
            'secret' => $site['wxapp_secret'],
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $ret = Http::get("https://api.weixin.qq.com/sns/jscode2session", $params);

        $data = (array)json_decode($ret, true);

        if (isset($data['openid'])) {
            //如果有传Token
            if ($this->token) {
                $this->auth->init($this->token);
                //检测是否登录
                if ($this->auth->isLogin()) {
                    $third = Third::where(['openid' => $data['openid'], 'platform' => 'wxapp'])->find();
                    if ($third && $third['user_id'] == $this->auth->id) {
                        $this->success("登录成功", ['userInfo' => $this->auth->getUserinfo()]);
                    }
                }
            }

            $platform = 'wxapp';

            $result = [
                'openid' => $data['openid'],
                'userinfo' => [
                    'nickname' => '',
                ],
                'access_token' => $data['session_key'],
                'refresh_token' => '',
                'expires_in' => isset($data['expires_in']) ? $data['expires_in'] : 0,
            ];

            $res = Service::connect($platform, $result);

            if ($res) {
                $auth = Auth::instance();
                $userInfo = $auth->getUserinfo();

                $this->success("登录成功", ['token' => $userInfo['token'], 'bindMoblie' => $userInfo['mobile'] ? 1 : 0]);
            } else {
                $this->error("连接失败");
            }
        } else {
            $this->error("错误码：" . $data['errcode'] . '，' . $data['errmsg']);
        }
    }

    /**
     * 解密微信小程序手机号
     */
    public function decryptMobile()
    {
        $user = $this->auth->getUser();
        $encryptedData = $this->request->post('encryptedData');
        $iv = $this->request->post('iv');

        if (empty($encryptedData) || empty($iv)) {
            $this->error('参数不正确');
        }

        //获取微信小程序配置信息
        $site = Config::get('site');

        $third_info = Third::get(['user_id' => $user->id]);

        if (empty($third_info)) {
            $this->error('用户未授权');
        }

        $sessionKey = $third_info->access_token;

        $crypt = new \app\api\library\encryption\WXBizDataCrypt($site['wxapp_appid'], $sessionKey);

        $errCode = $crypt->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $this->success('获取手机号成功', (array)json_decode($data, true));
        } else {
            $this->error('获取手机号失败，错误码：' . $errCode);
        }
    }

    /**
     * 绑定主手机号
     */
    public function bindMainMobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');

        if (!$mobile) {
            $this->error(__('Invalid parameters'));
        }

        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }

        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('手机号码已存在'));
        }

        $customer = \app\api\model\aurora\Customer::where('mobile', $mobile)->find();

        if ($customer) {
            //是否未绑定会员id
            if (empty($customer['user_id'])) {
                //删除原有的主手机号
                \app\api\model\aurora\Customer::where(['user_id' => $user->id, 'main' => 1])->delete();

                $verification = $user->verification;
                $verification->mobile = 1;
                $user->verification = $verification;
                $user->mobile = $mobile;
                $user->save();

                //关联会员id
                $customer->user_id = $user->id;
                $customer->main = 1;
                $customer->save();

                $this->success('绑定成功');
            }

            $this->error(__('手机号码已存在'));
        }

        DB::startTrans();

        try {
            //删除原有的主手机号
            \app\api\model\aurora\Customer::where(['user_id' => $user->id, 'main' => 1])->delete();

            $verification = $user->verification;
            $verification->mobile = 1;
            $user->verification = $verification;
            $user->mobile = $mobile;
            $user->save();

            //保存会员手机号-存入会员手机关联表
            $res = \app\api\model\aurora\Customer::create(['user_id' => $user->id, 'mobile' => $mobile, 'main' => 1]);

            if (!$res) {
                throw new Exception('绑定失败');
            }

            Db::commit();

            $this->success('绑定成功');
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

    /**
     * 添加手机号
     */
    public function addMobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->post('mobile');
        $captcha = $this->request->post('captcha');

        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }

        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }

        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('手机号码已存在'));
        }

        $result = Sms::check($mobile, $captcha, 'bindmobile');

        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }

        $customer = \app\api\model\aurora\Customer::where('mobile', $mobile)->find();

        if ($customer) {
            //是否未绑定会员id
            if (empty($customer['user_id'])) {
                //关联会员id
                $customer->user_id = $user->id;
                $customer->save();

                $this->success('添加成功');
            }

            $this->error(__('手机号码已存在'));
        }

        //保存数据
        $res = \app\api\model\aurora\Customer::create(['user_id' => $user->id, 'mobile' => $mobile, 'main' => 0]);

        if (!$res) {
            $this->error('添加失败');
        }

        $this->success('添加成功');
    }

    /**
     * 解绑手机号
     */
    public function unbindMobile()
    {
        $user = $this->auth->getUser();
        $id = $this->request->post('id');

        if (!$id) {
            $this->error(__('Invalid parameters'));
        }

        $data = \app\api\model\aurora\Customer::where(['id' => $id, 'user_id' => $user->id])->find();

        if (empty($data)) {
            $this->error('未绑定该手机号');
        }

        if ($data['main'] == 1) {
            $this->error('该手机号不能解绑');
        }

        //删除关联的手机信息
        $res = \app\api\model\aurora\Customer::where(['id' => $id, 'user_id' => $user->id])->delete();

        if (!$res) {
            $this->error('解绑失败');
        }

        $this->success('解绑成功');
    }

    /**
     * 会员极光信息列表
     */
    public function getList()
    {
        $user = $this->auth->getUser();

        $customer_list = \app\api\model\aurora\Customer::where(['user_id' => $user->id])->field('id,mobile,extraInfo,main')->order('main', 'desc')->select();

        $customer_list = collection($customer_list)->toArray();

        if (is_array($customer_list) && count($customer_list) > 0) {
            foreach ($customer_list as $key => $customer) {
                $customer_list[$key]['extraInfo'] = $customer['extraInfo'] ? (array)json_decode($customer['extraInfo'], true) : [];
            }
        }

        $this->success('', $customer_list);
    }

    /**
     * 同步极光车厂会员信息
     */
    public function syncInfo()
    {
        $mobile = $this->request->post('mobile');//手机号码
        // /a就相当于告诉解析器我要获取一个数组
        $extraInfo = $this->request->post('extra');//会员额外信息为此字段

        if (!empty($mobile) && !empty($extraInfo)) {
            //手机号是否已存在
            $customer = \app\api\model\aurora\Customer::where('mobile', $mobile)->find();

            if ($customer) {
                $customer->extraInfo = htmlspecialchars_decode($extraInfo);
                $customer->save();
            } else {
                \app\api\model\aurora\Customer::create(['mobile' => $mobile, 'extraInfo' => htmlspecialchars_decode($extraInfo), 'main' => 0]);
            }

            $this->success('同步成功');
        } else {
            $this->error('参数丢失');
        }
    }
}
