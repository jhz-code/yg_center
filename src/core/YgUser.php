<?php


namespace Yg\YgCenter\core;


use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use Yg\YgCenter\funcs\YgFunction;
use Yg\YgCenter\model\UserLoginModel;
use Yg\YgCenter\model\UserSourceModel;


/**
 * 用户数据操作
 * Class YgUser
 * @package Yg\YgCenter\lib
 */
class YgUser
{


    /**
     * 账户登录 并返回用户信息
     * @param $account
     * @param $password
     * @return array|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    function Login($account,$password): array|string
    {
        $find = UserLoginModel::where("userphone = '{$account}' or email = '{$account}' ")
            ->field('nickname,headimgurl,sex,truename,auth_id,level,isblack,ispass,password')->find();
        if($find && password_verify($password, $find['password'])) {
            if(empty($find['md5password'])){
                UserLoginModel::where(['id' => $find['id']])->update([
                    'md5password' => YgFunction::YgMd5String("ygxsj_.",$password)
                ]);
            }
            return $find->toArray();
        }else{
            return  '';
        }
    }


    /**
     * 通过微信登录
     */
    function LoginByWx(){

    }


    /**
     * 通过短信登录系统
     */
    function LoginBySms(){

    }





    /**
     * 创建用户资源
     * @param string $from
     * @param array $data
     * @return false|Model|UserSourceModel
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    function create_use_source(string $from,array $data): bool|UserSourceModel|Model
    {
        if(!UserLoginModel::where("userphone = '{$data['account']}' or email = '{$data['account']}' ")->find()){
            $data['from'] = $from;
            return UserSourceModel::create($data);
        }else{
            return false;
        }
    }








}