<?php


namespace Yg\YgCenter\lib;


use think\Exception;
use think\facade\Env;
use Yg\YgCenter\model\UserSessionModel;

class YgSession
{

    /**
     * 生成token
     * @param int $uid
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function generate(int $uid): string
    {
        $newToken = md5(md5(Env::get('TOKEN_KEY','ygxsj').$uid.time()));
        $userToken = UserSessionModel::where('_id',$uid)->find();
        if (empty($userToken)){
            UserSessionModel::insert([
                '_id' => $uid,
                'token' => $newToken,
                'exp' => (int)Env::get('TOKEN_EXP',86400*15) +time()
            ]);
        }else{
            UserSessionModel::where('_id',$uid)->update([
                'token' => $newToken,
                'exp' => (int)Env::get('TOKEN_EXP',86400)*15 +time()
            ]);
        }
        return $newToken;
    }

    /**
     * 验证Token并返回uid
     * @param string $token
     * @return int
     * @throws /Exception
     */
    public static function verification(string $token): int
    {
        try {
            $userToken = UserSessionModel::where('token',$token)->find();
            if (empty($userToken)){
                throw new Exception(lang('user.logininvalid'),config("status.not_login"));
            }
            if ($userToken->exp <= time()){
                throw new Exception(lang('user.logininvalid'),config("status.not_login"));
            }
            // 延长exp
            UserSessionModel::where('_id',$userToken->_id)->update([
                'exp' => (int)Env::get('TOKEN_EXP',86400*15) +time()
            ]);
            return (int)$userToken->_id;
        }catch (Exception $exception){
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 验证Token并返回uid
     * @param string $token
     * @return int
     * @throws /Exception
     */
    public static function verificationAndNotException(string $token): int
    {
        try {
            $userToken = UserSessionModel::where('token',$token)->find();
            if (empty($userToken)){
                return 0;
            }
            if ($userToken->exp <= time()){
                return 0;
            }
            // 延长exp
            UserSessionModel::where('_id',$userToken->_id)->update([
                'exp' => (int)Env::get('TOKEN_EXP',86400*15) +time()
            ]);
            return (int)$userToken->_id;
        }catch (Exception $exception){
            return 0;
        }
    }

    /**
     * 仅验证
     * @param string $token
     * @throws Exception
     */
    public static function verificationOnly(string $token)
    {
        self::verification($token);
    }

}