<?php

namespace app\api\model\aurora;

use think\Model;

/**
 * 极光会员信息模型
 */
class Customer extends Model
{
    // 表名
    protected $name = 'aurora_customer';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];
}
