<?php


namespace Yg\YgCenter\funcs;


class YgFunction
{


    /**
     * 加密函数
     * @param string $key
     * @param string $password
     * @return false|string
     */
    static function YgMd5String(string $key,string $password){
        return  substr(md5($key.md5($password)),8,16);
    }




}