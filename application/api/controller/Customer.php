<?php

namespace app\api\controller;

use app\common\controller\Api;
use fast\Http;
use think\Db;

/**
 * 接口
 */
class Customer extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['editCustomer'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    /**
     * 获取银豹会员信息
     */
    public function getCustomer()
    {
        $customer_list = Db::name('aurora_customer')->where(['phone' => $this->auth->mobile])->select();

        $this->success('', $customer_list);
    }

    /**
     * 更新会员额外信息
     */
    public function editCustomerExtra()
    {
        $uid = $this->request->post('uid');//TODO 暂定会员UID为标识
        // /a就相当于告诉解析器我要获取一个数组
        $extraInfo = $this->request->post('extraInfo/a');//TODO 暂定会员额外信息为此字段

        if(!empty($uid) && !empty($extraInfo)){
            $params = [];
            $params['extraInfo'] = json_encode($extraInfo);//json格式
            //更新会员额外信息
            Db::name('aurora_customer')->where(['customerUid' => $uid])->update($params);
        }
    }
}
