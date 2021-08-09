<?php


namespace Yg\YgCenter\model;

use think\Model;

/**
 * 用户登录模型
 * Class UserLoginModel
 * @package Yg\YgCenter\model
 */

class UserLoginModel extends  Model{


    // 设置当前模型对应的完整数据表名称
    protected $table;
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    public function __construct(array $data = [])
    {
        $this->table = env('database.prefix', '')."users_login";
        parent::__construct($data);
    }


}