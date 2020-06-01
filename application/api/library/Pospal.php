<?php

namespace app\api\library;

use fast\Http;
use think\Cache;

/**
 * 银豹开放平台接口
 */
class Pospal
{
    const APP_ID = 'BCDA3D2826365B67CFA2C5E0A5D50FB2';
    const APP_KEY = '618016368838948685';

    /**
     * 接口地址
     * @var string
     */
    public $url = '';
    /**
     * 请求体
     * @var array
     */
    public $data = [];
    /**
     * @var string
     */
    public $user_agent = '';
    /**
     * 请求的方法 默认为POST请求
     * @var string
     */
    public $method = 'POST';
    /**
     * 签名
     * @var string
     */
    public $signature = '';

    /**
     * 毫秒时间戳
     * @var string
     */
    public $timestamp = '';

    /**
     * 根据会员手机号查询会员
     * @param $mobile
     * @return array
     */
    public function queryBytel($mobile)
    {
        $this->url = 'https://area41-win.pospal.cn:443/pospal-api2/openapi/v1/customerOpenapi/queryBytel';
        $this->data = [
            'appId' => self::APP_ID,
            'customerTel' => $mobile
        ];
        $this->user_agent = 'queryBytel';//openApi
        $result = $this->sendRequest();
        return $result;
//        if ($result['status'] == 'success') {
//            if (!empty($result['data']) && is_array($result['data'])) {
//                return ['status' => 1, 'data' => $result['data']];
//            } else {
//                return ['status' => 0, 'msg' => '获取信息失败'];
//            }
//        } else {
//            return ['status' => 0, 'msg' => '错误信息：' . $result['messages'][0]];
//        }
    }

    /**
     * 分页查询全部会员
     * @param string $parameterType 从返回结果中直接取出用于回传，不能变其值
     * @param string $parameterValue    从返回结果中直接取出用于回传，不能变其值
     * @return array
     */
    public function queryCustomerPages($parameterType = '', $parameterValue = '')
    {
        $this->url = 'https://area41-win.pospal.cn:443/pospal-api2/openapi/v1/customerOpenApi/queryCustomerPages';

        $this->data = [
            'appId' => self::APP_ID,
            'postBackParameter' => [//分页查询回传到服务器的参数结构从第二页开始必须回传，如果没传，每次查询都是第一页
                'parameterType' => $parameterType,
                'parameterValue' => $parameterValue
            ]
        ];
        $this->user_agent = 'queryCustomerPages';//openApi
        $result = $this->sendRequest();
        return $result;
    }

    /**
     * 发送接口请求
     * @return array
     */
    private function sendRequest()
    {
        $this->timestamp = $this->millisecondTime();//毫秒时间戳
        //签名
        $this->signature = $this->createSignature();
        //头部信息
        $header = $this->setHeader();
        //curl参数设置
        $options = [
            //CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => $header
        ];

        $response = Http::sendRequest($this->url, json_encode($this->data), $this->method, $options);
        $response = $response['ret'] ? $response['msg'] : '';
        $result = (array)json_decode($response, true);
        return $result;
    }

    /**
     * 生成签名
     * @param $jsonData
     * @return string
     */
    public function createSignature()
    {
        $signature = strtoupper(md5(self::APP_KEY . json_encode($this->data)));
        return $signature;
    }

    /**
     * 设置头部信息
     * @param $agent
     * @param $timestamp
     * @param $signature
     * @return array
     */
    public function setHeader()
    {
        $data = [
            'User-Agent:' . $this->user_agent,
            'Content-Type: application/json; charset=utf-8',
            'accept-encoding: gzip,deflate',
            'time-stamp:' . $this->timestamp,//毫秒时间戳
            'data-signature:' . $this->signature
        ];
        return $data;
    }

    /**
     * 返回当前的毫秒时间戳
     * @return float
     */
    public function millisecondTime()
    {
        list($msec, $sec) = explode(' ', microtime());

        $time = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        return $time;
    }
}
