<?php

use Phalcon\Mvc\User\Component;
/**
* curl工具类
* realvik 2014-08-07 14:00
*/
/*
$url = 'http://video.sina.com.cn/l/pl/sportstv/1687894.html';
$curlHelper = new CurlHelper();
echo $curlHelper->get($url);
*/
class CurlHelper extends Component
{
    private $headers = array();
    
    function __construct($user_agent='', $reffer='')
    {

        $headers = array(

            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",

            "Cache-Control: no-cache",

            "Pragma: no-cache",

        );

        if(!empty($reffer)){
            $headers[] = 'Referer: '.$reffer;
        }

        if(!empty($user_agent)){
            $headers[] = 'User-Agent: '.$user_agent;
        }else{
            //$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36";
            $headers[] = "User-Agent: Mozilla/5.0 (iPad; CPU OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53";
        }

        $this->headers = $headers;

    }

    function post($url, $post_data,$time_out=3){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);

        if(0 === strpos(strtolower($url), 'https')) {

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在

        }

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;

    }

    function get($url,$time_out=3){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, $time_out);

        if(0 === strpos(strtolower($url), 'https')) {

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在

        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;

    }

}

