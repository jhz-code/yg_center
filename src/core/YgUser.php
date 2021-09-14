<?php


namespace Yg\YgCenter\core;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use Yg\YgCenter\funcs\YgFunction;
use Yg\YgCenter\lib\wxplatform\WxLogin;
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
                "create_time" => $data['create_time'],
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
       $insert['email'] = $data['email'];
       $insert['userphone'] = $data['userphone'];
       $insert['password'] = $data['password'];
       $insert['md5password'] =  $data['md5password'] ;
       $insert['login_ip'] = "" ;
       UserLoginModel::create($insert);
    }


    /**
    * 获取用户资料
     * @param string $account //手机号//邮箱
     * @param string $from //为空输出所有关联数据
     * @param string $account
     * @param string $from
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     *
     */
    static function getUserInfo(string $account,string $from = "")
    {
            if($from){
                $result  =  UserSourceModel::where("userphone = '{$account}' or email = '{$account} or auth_id = '{$account}'  ")->where(['from'=>$from])->find();
                $userInfo['username '] = $result['auth_id'];
                $userInfo['phone'] = $result['userphone'];;
                $userInfo['email'] = $result['email'];;
                $userInfo['true_name'] = $result['truename'];
                $userInfo['pid'] = $result['equal_id'];
                $userInfo['gender'] = $result['sex'];
                $userInfo['nickname'] = $result['nickname'];
                $userInfo['al_id'] = $result['level'];
                $userInfo['pic_link'] = $result['headimgurl'];
                $userInfo['wx_openid'] = $result['wxopenid'];
                $userInfo['wx_unionid'] = $result['wxunionid'];
                $userInfo['create_time'] = $result['create_time'];;
                $userInfo['password'] = '';
                $userInfo['from'] = $result['from'];
                return $userInfo;
            }else{
                $list =  UserSourceModel::where("userphone = '{$account}' or email = '{$account}' or auth_id = '{$account}'  ")->select();
                $userList = [];
                foreach ($list as $key=>$value){
                    $userList[$key]['username '] = $value['auth_id'];
                    $userList[$key]['phone'] = $value['userphone'];;
                    $userList[$key]['email'] = $value['email'];;
                    $userList[$key]['true_name'] = $value['truename'];
                    $userList[$key]['pid'] = $value['equal_id'];
                    $userList[$key]['gender'] = $value['sex'];
                    $userList[$key]['nickname'] = $value['nickname'];
                    $userList[$key]['al_id'] = $value['level'];
                    $userList[$key]['pic_link'] = $value['headimgurl'];
                    $userList[$key]['wx_openid'] = $value['wxopenid'];
                    $userList[$key]['wx_unionid'] = $value['wxunionid'];
                    $userList[$key]['create_time'] = $value['create_time'];;
                    $userList[$key]['password'] = '';
                    $userList[$key]['from'] = $value['from'];
                }
                return $userList;
            }
    }








    /**
     * @param string $token //通过第三方标识符获取用户信息  openID//unionID//authId
     * @return array|Model|UserSourceModel|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function getUserInfoByThirdToken(string $token){
        return UserSourceModel::where("wxunionid = '{$token}' or wxopenid = '{$token}' or auth_id = '{auth_id}' ")->find();
    }



    /**
     * 判断用户是否存在于经销商
     * @param string $account //手机号//邮箱//授权//openID/unionID//
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function checkUserExist(string $account)
    {
           //检测手机号或邮箱是否存在
        if(UserLoginModel::where("userphone = '$account' or email = '$account' ")->find()){
            return false;
            //检测授权编号是否存在
        }else if(UserSourceModel::where("wxunionid = '{$account}' or wxopenid = '{$account}' or auth_id = '{$account}'  ")->find()){
            return false;
        }else{
            return true;
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
        return  UserSourceModel::where(['uid'=>$uid,'from'=>$from])->update($data);
    }


    /**
     * 删除用户相关数据
     * @param string $from
     * @param $uid
     * @return bool
     */
    static function delete_user(string $from,$uid){
        return  UserSourceModel::where(['uid'=>$uid,'from'=>$from])->delete();
    }


    /**
     * 通过手机号获取用户最高等级
     * @param string $mobile
     * @return mixed|UserSourceModel
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static function getUserLevel(string $mobile){
        //查询当前用户是否是经销商
        return UserSourceModel::where(['userphone'=>$mobile])->field('level,from')->order('level','desc')->select()[0];
    }




    /**
     * 账户登录 并返回用户信息
     * @param $account
     * @param $password
     * @param string $from
     * @return array|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static  function Login($account, $password, string $from = "YG_YGXSj")
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
            $UserInfo = UserSourceModel::where("userphone = '{$account}' or email = '{$account}' ")->where(['from'=>$from])->find();
            if($UserInfo){
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



}