<?php

namespace app\api\controller\crontab;

use think\Controller;
use think\Db;

/**
 * 定时接口
 */
class Index extends Controller
{
    public function _initialize()
    {
        parent::_initialize();

        // 清除错误
        error_reporting(0);

        // 设置永不超时
        set_time_limit(0);
    }

    public function customer()
    {
        $this->syncPospalCustomer();
    }

    /**
     * 同步银豹会员信息
     * @param string $parameterType
     * @param string $parameterValue
     */
    private function syncPospalCustomer($parameterType = '', $parameterValue = '')
    {
        $pospal = new \app\api\library\Pospal;

        $result = $pospal->queryCustomerPages($parameterType, $parameterValue);

        if ($result['status'] == 'success') {
            if (!empty($result['data']) && is_array($result['data'])) {
                $page_size = $result['data']['pageSize'];
                $list = $result['data']['result'];
                if (is_array($list) && count($list) > 0) {
                    foreach ($list as $value) {
                        unset($value['customrUid']);
                        //查询会员是否存在表中，若无则插入数据，有则更新会员信息
                        $customer = Db::name('aurora_customer')->where(['customerUid' => $value['customerUid']])->find();
                        if (!empty($customer)) {
                            Db::name('aurora_customer')->where(['customerUid' => $value['customerUid']])->update($value);
                        } else {
                            Db::name('aurora_customer')->insert($value);
                        }
                    }
                    //本次查询预期从数据库中取出记录数，如果结果集的长度大于等于pageSize，则需要进行下一页查询
                    if (count($list) >= $page_size) {
                        $this->syncPospalCustomer($result['data']['postBackParameter']['parameterType'], $result['data']['postBackParameter']['parameterValue']);
                    }
                }
            }
        }
    }
}
