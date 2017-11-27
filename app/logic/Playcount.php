<?php 
namespace logic;
use logic\GetContent;

class Playcount{

	public static function duration($url){
		if( false !== strpos($url, 'qq.com')){
			return self::qq($url);
		}elseif(false !== strpos($url, 'youku.com')){
			return self::youku($url);
		}elseif(false !== strpos($url, 'weibo.com')){
			return self::weibo($url);
		}elseif(false !== strpos($url, 'pptv.com')){
			return self::pptv($url);
		}elseif(false !== strpos($url, 'cctv.com')){
			return self::cctv($url);
		}elseif(false !== strpos($url, 'sina.com')){
			return self::sina($url);
		}else{
			return 0;
		}

	}


	public static function qq($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/duration: \"([\d|\.]+)\",/isU',$html,$match)){
            return duration_format(trim($match[1]));
        }else{
        	return 0;
        }
        

	}

	public static function youku($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/seconds:\"([\d|\.]+)\",/isU',$html,$match)){
            return duration_format(trim($match[1]));
        }else{
        	return 0;
        }
	}

	public static function weibo($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/play_count=[\d|\.]+&duration=([\d|\.]+)&/isU',$html,$match)){
            return duration_format(trim($match[1]));
        }else{
        	return 0;
        }
	}
 

    public static function pptv($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/duration\":([\d|\.]+),/isU',$html,$match)){
            return duration_format(trim($match[1]));
        }else{
        	return 0;
        }
	}

	public static function sina($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/video_id: (\d+),/isU',$html,$match)){
        	$realurl = "http://hpi.video.sina.com.cn/public/video/play?video_id=$match[1]&appver=1.1&appname=video&applt=web&tags=video&player=flash&uid=null&pid=7943&tid=0&plid=5001&prid=1-1509585071966-412-5d4113990bb5&referrer=&r=$url&v=5.0.1.55.1025.01&ssid=$match[1]_null_1509674375863_994_923323";
        	$realhtml = $content->html($realurl);
        	if(preg_match('/width\":\"480\",\"length\":\"(\d+)\",\"definition/isU',$realhtml,$ma)){
        		$seconds = round($ma[1]/1000);
                return duration_format($seconds);
        	}else{
        		return 0;
        	}
            
        }else{
        	return 0;
        }
	}

	public static function cctv($url) {
		$content = new GetContent;
        $html = $content->html($url);
        if(preg_match('/\"videoCenterId\",\"(.*)\"\);/isU',$html,$match)){
        	$realurl = "http://api.cntv.cn/video/videoinfoByGuid?serviceId=cbox&guid=$match[1]&cb=?&t=jsonp";
        	$realhtml = $content->html($realurl);
        	if(preg_match('/"len":"(.*)","channel/isU',$realhtml,$ma)){
                return duration_format(trim($ma[1]));
        	}else{
        		return 0;
        	}
            
        }else{
        	return 0;
        }
	}



}