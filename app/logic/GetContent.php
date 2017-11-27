<?php 
namespace logic;

class GetContent{
    public $ch;
    public $ip;
    public $timeout;

	/**
     * URL请求页面内容
     * @param $url 
     * @param $toEncoding  要输出的编码格式
     * @param $fromEncoding  待转换的编码格式
     * @return string
     */
    public  function html($url,$toEncoding='',$fromEncoding='')
    {

        $result= $this->getcurl($url);
        $fromEncoding = $fromEncoding?$fromEncoding:$this->getEncode($result);
        //echo $fromEncoding."\r\n";
        if ($toEncoding) {
            //编码转换
            $result = $this->arrayConvertEncoding($result,$toEncoding,$fromEncoding);
        }
        return $result;
    }


    /**
     * 获取文件编码
     */
    public  function getEncode($string)
    {
        return mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    }

    /**
     * 转换数组值的编码格式                
     */
    public  function arrayConvertEncoding($arr, $toEncoding, $fromEncoding)
    {
        eval('$arr = '.iconv($fromEncoding, $toEncoding.'//IGNORE', var_export($arr,TRUE)).';');
        return $arr;
    }

 
    protected  function getcurl($url)
    {
   
        $this->ch = curl_init();
        $this->ip = '220.181.108.'.rand(1,255);
        $this->timeout = 10;
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_setopt($this->ch,CURLOPT_TIMEOUT,0);
        //伪造百度蜘蛛IP  
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array('X-FORWARDED-FOR:'.$this->ip.'','CLIENT-IP:'.$this->ip.'')); 
        //伪造百度蜘蛛头部
        curl_setopt($this->ch,CURLOPT_USERAGENT,"Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)");
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($this->ch,CURLOPT_HEADER,0);
        curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT,$this->timeout);
        curl_setopt($this->ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,false);
        $content = curl_exec($this->ch);

        if($content === false)
        {//输出错误信息
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "",
                    "timeout" => 10,
                ],
            ];
            $context = stream_context_create($opts);
            $content = @file_get_contents($url,false, $context);
            if(empty($content)){
                $no = curl_errno($this->ch);
                switch(trim($no))
                {
                    case 28 : $error = '访问目标地址超时'; break;
                    default : $error = curl_error($this->ch); break;
                }
                echo $error;  
            }else{
                return $content;
            }
            
        }
        else
        {
            return $content;
        }
    }



}