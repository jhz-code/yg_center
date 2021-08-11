<?php


namespace Yg\YgCenter\core;


use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use Yg\YgCenter\funcs\YgFunction;
use Yg\YgCenter\lib\wxplatform\WxLogin;
use Yg\YgCenter\lib\YgSession;
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
     * @param $from
     * @return array|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */

    function Login($account,$password,$from)
    {
        $find = UserLoginModel::where("userphone = '{$account}' or email = '{$account}' ")->find();
        //用户登陆成功,检索系统用户
        if($find && password_verify($password, $find['password'])) {
            if(empty($find['md5password'])){
                UserLoginModel::where(['id' => $find['id']])->update([
                    'md5password' => YgFunction::YgMd5String("ygxsj_.",$password)
                ]);
            }
            //检索系统用户 , 返回关联用户数据
            $UserInfo = UserSourceModel::where("userphone = '{$account}' or email = '{$account}' ")->where(['from'=>$from])
                ->field('nickname,headimgurl,sex,truename,auth_id,level,isblack,ispass,password')
                ->find();
            if($UserInfo){
                return $UserInfo->toArray();
            }else{
                return [];
            }
        }else{
            return  '';
        }
    }


    /**
     * 通过微信登录
     */
    function LoginByWx($appId,$appSecret,$from)
    {
        if(!empty($code)){
            $wxPlatform = new  WxLogin($appId,$appSecret);
            $result = $wxPlatform->getAccessToken($code);
            $UserInfo = $wxPlatform->getUserInfo($result['openid'], $result['access_token']);
            $res = UserSourceModel::where(['wxopenid'=>$UserInfo['wxopenid'],'from'=>$from])->find();
            if($res){
                return  $res[1];
            }else{
                return  [];
            }
        }else{
            return [];
        }
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
    function create_use_source(string $from,array $data)
    {
        if(!UserLoginModel::where("userphone = '{$data['account']}' or email = '{$data['account']}' ")->find()){
            $data['from'] = $from;
            return UserSourceModel::create($data);
        }else{
            return false;
        }
    }








}