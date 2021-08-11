<?php


namespace Yg\YgCenter\lib;


use Yg\YgCenter\model\UserSourceModel;

class YgUserCheck
{


    /**
     * 邮箱匹配
     * @param string $str
     * @return false|int
     */
    static function isEmail(string $str): bool|int
    {
        return preg_match_all("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $str);
    }

    /**
     * 账户匹配
     * @param string $str
     * @return false|int
     */
    static  function isPhone(string $str): bool|int
    {
        return preg_match_all("/^[0-9]*$/", $str);
    }


    /**
     * 电话号码匹配
     * @param string $str
     * @return false|int
     */
    static function isRealPhone(string $str): bool|int
    {
        return preg_match_all("/^1[3456789]\d{9}$/", $str);
    }


    /**
     * 身份证匹配
     * @param string $str
     * @return bool|int|null
     */
    static function isIdCard(string $str): bool|int|null
    {
        return preg_match_all("/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/", $str);
    }




}