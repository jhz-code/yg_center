<?php


namespace Yg\YgCenter\model;


use think\Model;

/**
 * 用户数据模型
 * Class UserSourceModel
 * @package Yg\YgCenter\model
 */

class UserSourceModel extends Model
{

    // 设置当前模型对应的完整数据表名称
    protected $table;
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    protected $connection = 'YG_CENTER';

    public function __construct(array $data = [])
    {
        $this->table = env('database.prefix', '')."users_source";
        parent::__construct($data);
    }

}