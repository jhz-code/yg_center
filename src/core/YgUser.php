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
                "create_time" => $data['create_time']??0,
                "update_time" => time(),
                "equal_id" => $data['equal_id']??0,
                "level" => $data['level']??0,
                'userphone' => $data['userphone']??"",
                'email' =>  $data['email']??"",
                'useraccount'=>$data['useraccount']??"",//用户账户
                'uid'=>$data['id'],//用户账户
                'auth_id'=>$data['auth_id']??"",//用户账户
                'state'=>$data['state'],//用户账户
                'ispass'=>$data['ispass'],//用户账户
                'auth_time'=>$data['auth_time'],//用户账户
                'auth_endtime'=>$data['auth_endtime'],
                'isblack'=>$data['isblack'],
                'headimgurl'=>$data['headimgurl']??'',
                'truename'=>$data['truename']??'',
                'birthday'=>$data['birthday'],
                'sex'=>$data['sex']??0,
                'country'=>$data['country']??'',
                'province'=>$data['province']??'',
                'city'=>$data['city']??'',
                'area'=>$data['area']??'',
                'address'=>$data['address']??'',
                'idcard_type'=>$data['idcard_type']??0,
                'idcard_num'=>$data['idcard_num']??'',
                'referee'=>$data['referee'],
                'nid'=>$data['nid']??0,
                'pid'=>$data['pid']??0,
                'from'=>$data['from'],
                'wxunionid'=>$data['wxunionid']??'',
                'wxopenid'=>$data['wxopenid']??'',
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
       if(!UserLoginModel::where("userphone = '{$data['account']}' or email = '{$data['account']}' ")->find()){
           if(!UserLoginModel::where("userphone = '{$data['account']}'")->find() && !UserLoginModel::where(['email'=>$data['account']])->find()){
               $insert['email'] = $data['email'];
               $insert['userphone'] = $data['userphone'];
               $insert['password'] = $data['password'];
               $insert['md5password'] =  $data['md5password'] ;
               $insert['login_ip'] = "" ;
               UserLoginModel::create($insert);
           }else{
               if($find = UserLoginModel::where("userphone = '{$data['account']}'")->find()){
                   UserLoginModel::where(['id'=>$find["id"]])->update(['email'=>$data['account']]);
               }else{
                   UserLoginModel::where(['email'=>$find["account"]])->update(['userphone'=>$data['account']]);
               }
           }
       }
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
                $result  =  UserSourceModel::where("userphone = '{$account}' or email = '{$account}' or auth_id = '{$account}'")->where(['from'=>$from])->find();
                $userInfo['id'] = $result['id'];
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
                $userInfo['create_time'] = $result['create_time'];
                $userInfo['password'] = self::getUserPassword($account);
                $userInfo['from'] = $result['from'];
                return $userInfo;
            }else{
                $list =  UserSourceModel::where("userphone = '{$account}' or email = '{$account}' or auth_id = '{$account}'")->where('ispass = 1')->order('level',"desc")->select();
                $userList = [];
                foreach ($list as $key=>$value){
                    $userList[$key]['id'] = $value['id'];
                    $userList[$key]['username'] = $value['auth_id'];
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
                    $userList[$key]['password'] = self::getUserPassword($account);
                    $userList[$key]['from'] = $value['from'];
                }
                return $userList;
            }
    }



    /**
     * 根据对应账户获取对应账户密码
     * @param string $account
     * @return mixed|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
   static function getUserPassword(string $account){
       if($result = UserLoginModel::where("userphone = '{$account}' or email = '{$account}' ")->find()){
           return $result['password'];
       }else if($result = UserSourceModel::where("auth_id = '{$account}' or wxunionid = '{$account}'  or wxopenid = '{$account}' ")->find()){
           return $result['password'];
       }else{
           return  '';
       }
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
        }else if(UserSourceModel::where("wxunionid = '{$account}' or wxopenid = '{$account}' or auth_id = '{$account}' ")->find()){
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
     * @param int $uid
     * @param array $data
     * @return UserSourceModel
     */
    static function updateUserInfo(int $uid,array $data)
    {
        return  UserSourceModel::where(['id'=>$uid])->update($data);
    }


    /**
     * 删除用户相关数据
     * @param string $from
     * @param $uid
     * @return bool
     */
    static function delete_user(int $uid){
        return  UserSourceModel::where(['id'=>$uid])->delete();
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
     * 账户登录
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
        if(self::checkUserExist($account))return ['code'=>0,'msg'=>'当前账户未注册'];
        $find = UserLoginModel::where("userphone = '{$account}' or email = '{$account}' ")->find();
        //用户登陆成功,检索系统用户
        if($find && password_verify($password, $find['password'])) {
            if(empty($find['md5password'])){
                UserLoginModel::where(['id' => $find['id']])->update([
                    'md5password' => YgFunction::YgMd5String("ygxsj_.",$password)
                ]);
            }
            return ['code'=>2,'msg'=>'密码校验成功'];
         }

        //手机号
        $findByAuthId = UserSourceModel::where("wxunionid = '{$account}' or wxopenid = '{$account}' or auth_id = '{$account}' ")->find();
        if($findByAuthId && $findByAuthId['userphone']){
            $find = UserLoginModel::where(['userphone'=>$findByAuthId['userphone']])->find();
            if(password_verify($password, $find['password'])) {
                if(empty($find['md5password'])){
                    UserLoginModel::where(['id' => $find['id']])->update([
                        'md5password' => YgFunction::YgMd5String("ygxsj_.",$password)
                    ]);
                }
                return ['code'=>2,'msg'=>'密码校验成功'];
            }
        }

        //邮箱
        if($findByAuthId && $findByAuthId['email']){
            $find = UserLoginModel::where(['email'=>$findByAuthId['email']])->find();
            if(password_verify($password, $find['password'])) {
                if(empty($find['md5password'])){
                    UserLoginModel::where(['id' => $find['id']])->update([
                        'md5password' => YgFunction::YgMd5String("ygxsj_.",$password)
                    ]);
                }
                return ['code'=>2,'msg'=>'密码校验成功'];

            }
        }
        return ['code'=>1,'msg'=>'抱歉，密码错误'];

    }



}