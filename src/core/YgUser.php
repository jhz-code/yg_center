<?php


namespace Yg\YgCenter\core;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use Yg\YgCenter\funcs\YgFunction;
use Yg\YgCenter\lib\wxplatform\WxLogin;
use Yg\YgCenter\lib\YgUserCheck;
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
   static   function Login($account,$password,$from)
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
               // self::userLoginUpdate($find['id']);
                return $UserInfo->toArray();
            }else{
                return [];
            }
        }else{
            return  "密码错误";
        }
    }


    /**
     * 通过微信登录
     */
     static   function LoginByWx($appId,$appSecret,$from)
      {
        if(!empty($code)){
            $wxPlatform = new  WxLogin($appId,$appSecret);
            $result = $wxPlatform->getAccessToken($code);
            $UserInfo = $wxPlatform->getUserInfo($result['openid'], $result['access_token']);
            $res = UserSourceModel::where(['wxopenid'=>$UserInfo['wxopenid'],'from'=>$from])->find();
            if($res){
                //返回用户信息
                return  $res[1];
            }else{
                return  [];
            }
        }else{
            return [];
        }
    }


    /**
     * 创建用户资源
     * @param string $from
     * @param array $data
     * @return string|Model|UserSourceModel
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function create_user_source(string $from,array $data)
    {
        if(!UserSourceModel::where("userphone = '{$data['account']}' or email = '{$data['account']}' ")->where(['from'=>$from])->find()){
            $data['from'] = $from;
            self::createUserLogin($data);
            return UserSourceModel::create([
                "create_time" => time(),
                "update_time" => time(),
                "equal_id" => $data['equal_id'],
                "level" => $data['level'],
                'userphone' => $data['userphone'],
                'email' =>  $data['email'],
                'useraccount'=>$data['useraccount'],//用户账户
                'uid'=>$data['id'],//用户账户
                'auth_id'=>$data['auth_id'],//用户账户
                'state'=>$data['state'],//用户账户
                'ispass'=>$data['ispass'],//用户账户
                'auth_time'=>$data['auth_time'],//用户账户
                'auth_endtime'=>$data['auth_endtime'],
                'isblack'=>$data['isblack'],
                'headimgurl'=>$data['headimgurl'],
                'truename'=>$data['truename'],
                'birthday'=>$data['birthday'],
                'sex'=>$data['sex'],
                'country'=>$data['country'],
                'province'=>$data['province'],
                'city'=>$data['city'],
                'area'=>$data['area'],
                'address'=>$data['address'],
                'idcard_type'=>$data['idcard_type'],
                'idcard_num'=>$data['idcard_num'],
                'referee'=>$data['referee'],
                'nid'=>$data['nid'],
                'pid'=>$data['pid'],
                'from'=>$data['from'],
                'wxunionid'=>$data['wxunionid'],
                'wxopenid'=>$data['wxopenid'],
            ]);
        }else{
            return "用户已存在";
        }
    }


    /**
     * 创建用户登录信息
     * @param array $data
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function createUserLogin(array $data){
       if(UserLoginModel::where("userphone = '{$data['account']}' or email = '{$data['account']}' ")->find())
       $insert['email'] = YgUserCheck::isEmail($data['email'])?$data['email']:"";
       $insert['userphone'] = YgUserCheck::isPhone($data['userphone'])?$data['userphone']:"";
       $insert['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
       $insert['md5password'] = YgFunction::YgMd5String("ygxsj_.", $data['password']) ;
       $insert['login_ip'] = YgFunction::getClientIP() ;
       UserLoginModel::create($insert);
    }


    /**
     * 通过账户读取用户信息
     * @param string $account
     * @param string $from //默认读取眼罩系统数据
     * @return array|Model|UserSourceModel|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function getUserInfoByAccount(string $account,string $from = 'YG_YGXSj'){
        return  UserSourceModel::where("userphone = '{$account}' or email = '{$account}' ")->where(['from'=>$from])
            ->find();
    }


    /**
     * 获取用户资料
     * @param string $account
     * @param string $from //为空输出所有关联数据
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function getUserInfo(string $account,string $from = ""){
        if(UserLoginModel::where("userphone = '$account' or email = '$account' ")->find()){
            if($from){
                return UserSourceModel::where("userphone = '{$account}' or email = '{$account}' ")->where(['from'=>$from])
                    ->find();
            }else{
                return UserSourceModel::where("userphone = '{$account}' or email = '{$account}' ")
                    ->select();
            }
        }else{
            return [];
        }
    }


    /**
     * 通过用户UID数据用户数据
     * @param int $uid
     * @param string $from
     * @return array|Model|UserSourceModel|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function getUserInfoByUid(int $uid,string $from){
        return  UserSourceModel::where(['uid'=>$uid,'from'=>$from])
            ->field('nickname,headimgurl,sex,truename,auth_id,level,isblack,ispass,password')
            ->find();
    }


    /**
     * 更新用户资料信息
     * @param string $from
     * @param $uid
     * @param array $data
     * @return UserSourceModel
     */
    static function updateUserInfo(string $from,$uid,array $data)
    {
        return  UserSourceModel::where(['uid'=>$uid,'from'=>$from])
            ->update($data);
    }


    /**
     * 删除用户相关数据
     * @param string $from
     * @param $uid
     * @return bool
     */
    static function delete_user(string $from,$uid){
        return  UserSourceModel::where(['uid'=>$uid,'from'=>$from])
            ->delete();
    }


    /**
     * 获取用户最高等级
     */
    static function getUserLevel(string $mobile){
        //查询当前用户是否是经销商
        return UserSourceModel::where(['userphone'=>$mobile])->field('level,from')->order('level','desc')->select()[0];
    }

}