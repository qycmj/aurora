<?php

namespace app\api\model\aurora;

use think\Model;

/**
 * 第三方登录模型
 */
class Third extends Model
{
    // 表名
    protected $name = 'aurora_third';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];
}
