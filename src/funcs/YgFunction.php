<?php


namespace Yg\YgCenter\funcs;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Exception;


class YgFunction
{


    /**
     * 加密函数
     * @param string $key
     * @param string $password
     * @return false|string
     */
    static function YgMd5String(string $key,string $password)
    {
        return  substr(md5($key.md5($password)),8,16);
    }


    /**
     * 获取随机值
     * 随机 -0-9 A -Z
     * @param [type] $len
     * @return string
     */
    static  function YgRandCode($len): string
    {
        $chars = array(
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = '';
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }


    /**
     * 二维码输出
     * @param $url
     * @param $size
     * @param string $logo
     * @param string $label
     * @return string
     * @throws Exception
     */
    function getQrcode($url, $size, string $logo = '', string $label = ''): string
    {
        $writer = new PngWriter();
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize($size)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        if($logo){
            $logo = Logo::create($logo)->setResizeToWidth(50);
        }
        if($label){
            $label = Label::create($label)->setTextColor(new Color(255, 0, 0));
        }
        $result = $writer->write($qrCode, $logo, $label);
        header('Content-Type: '.$result->getMimeType());
        return $result->getString();
    }


   static function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }




}