<?php


namespace Yg\YgCenter\lib\wxplatform;

use think\Exception;
use think\facade\Cache;
use Yg\YgCenter\funcs\YgFunction;

class WxLogin
{

    protected  $appId;
    protected  $appSecret;

    public function __construct(string $appId,string $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }


    /**
     * @return string
     *跳转获取code
     */
    public function getUrlCode(): string
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?";
        $param['appid'] = $this->appId;
        $param['redirect_uri'] = $this->getUrl();
        $param['response_type'] = "code";
        $param['scope'] = "snsapi_userinfo ";
        $param['state'] = YgFunction::YgRandCode(8);
        return $url . http_build_query($param) . "#wechat_redirect";
    }

    /**
     * @return string 获取当前url
     */
    public function getUrl(): string
    {
        $protocol =
            ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ?
                "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }



    /**
     * 通过url code 换取access_token
     * @param $code
     * @return mixed
     */
     function getAccessToken($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?";
        $param['appid'] = $this->appId;
        $param['secret'] = $this->appSecret;
        $param['code'] = $code;
        $param['grant_type'] = 'authorization_code';
        if ($res = self::Httprequest($url, $param)) {
            $result = json_decode($res, true);
            if (!isset($result['errcode'])) {
                if (isset($result['access_token'])) {
                    Cache::set("access_token", $result['access_token'], '5000');
                }
                if (isset($result['refresh_token'])) {
                    Cache::set("access_token", $result['refresh_token'], strtotime("+27 day"));
                }
                return json_decode($res, true);
            }else{
                return json_decode($result, true);
            }
        }else{
            return [];
        }
    }

    /**
     * @return mixed
     * 刷新token
     * @throws Exception
     */
    public function reFreshToken()
    {
        if (!Cache::get("refresh_token")){
            return false;
        }
        //TODO:刷新token失效
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?";
        $param['appid'] = $this->appId;
        $param['grant_type'] = "refresh_token";
        $param['refresh_token'] = Cache::get("refresh_token");
        if ($res = self::HttpRequest($url, $param)) {
            $result = json_decode($res, true);
            if (!isset($result['errcode'])) {
                if (isset($result['access_token'])) {
                    Cache::set("access_token", $result['access_token'], '5000');
                }
                if (isset($result['refresh_token'])) {
                    Cache::set("access_token", $result['refresh_token'], strtotime("+27 day"));
                }
                return $result;
            } else {
                throw new Exception($result['errmsg']);
            }
        }else{
            return [];
        }
    }

    /**
     * @param string $openId
     * @param string $token
     * 获取公众号用户资料信息
     * @return mixed
     * @throws Exception
     */
    public  function getUserInfo(string $openId, string $token)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?";
        $param['access_token'] = $token;
        $param['openid'] = $openId;
        $param['lang'] = "zh_CN ";
        if ($res = self::HttpRequest($url, $param, 'get')) {
            $result = json_decode($res, true);
            if (!isset($result['errcode'])) {
                return $result; //TODO：返回用户信息
            } else {
                throw new Exception($result['errmsg']);
            }
        }else{
            return [];
        }
    }



    /**
     * @param $url
     * @param string $method
     * @param array $param
     * @param array $header
     * @return bool|string
     * 封装请求方法
     */

    public static function HttpRequest($url, array $param, string $method = 'get', array $header = [])
    {
        if ($method === "get" && !empty($param)) {
            $url = $url . http_build_query($param);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (strtolower($method) === "post" && !empty($param)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }



}