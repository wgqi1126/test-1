<?php 
namespace logic;

class Ffmpeg{

    public static $KC_FFMPEG_PATH = 'C:\Users\admin\Downloads\ffmpeg\bin\ffmpeg -i "%s" 2>&1';


	public static function video_info($file) {
        
	    ob_start();
	    passthru(sprintf(self::$KC_FFMPEG_PATH, $file));
	    $info = ob_get_contents();
	    ob_end_clean();
	    // print_r($info);die;
	    // 通过使用输出缓冲，获取到ffmpeg所有输出的内容。
	    $ret = array();
	    // Duration: 01:24:12.73, start: 0.000000, bitrate: 456 kb/s
	    if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $info, $match)) {
	        $ret['duration'] = $match[1]; // 提取出播放时间
	        $da = explode(':', $match[1]); 
	        $ret['seconds'] = $da[0] * 3600 + $da[1] * 60 + $da[2]; // 转换为秒
	        $ret['start'] = $match[2]; // 开始时间
	        $ret['bitrate'] = $match[3]; // bitrate 码率 单位 kb
	    }

	    // Stream #0.1: Video: rv40, yuv420p, 512x384, 355 kb/s, 12.05 fps, 12 tbr, 1k tbn, 12 tbc
	    if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $info, $match)) {
	        $ret['vcodec'] = $match[1]; // 编码格式
	        $ret['vformat'] = $match[2]; // 视频格式 
	        $ret['resolution'] = $match[3]; // 分辨率
	        $a = explode('x', $match[3]);
	        $ret['width'] = $a[0];
	        $ret['height'] = $a[1];
	    }

	    // Stream #0.0: Audio: cook, 44100 Hz, stereo, s16, 96 kb/s
	    if (preg_match("/Audio: (\w*), (\d*) Hz/", $info, $match)) {
	        $ret['acodec'] = $match[1];       // 音频编码
	        $ret['asamplerate'] = $match[2];  // 音频采样频率
	    }

	    if (isset($ret['seconds']) && isset($ret['start'])) {
	        $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间
	    }

	    $ret['size'] = filesize($file); // 文件大小
	    return $ret;
	}	

	public static function get_dir_video($folder,$ext='.*',$urls=[]){

        if(!is_dir($folder)){
             return false;
        }

        $files = glob($folder.'*'.$ext);
        $res = [];

        foreach ($files as $k => $file) {
        	$duration = self::video_info($file);
        	$res[$file] = $duration;
        	$res[$file]['url'] = '';
        	if(!empty($urls) && is_array($urls)){
	        	foreach ($urls as $key => $url) {
	        		similar_text($file,$url,$percent);
	        		if($percent>60)$res[$file]['url'] = $url;
	        	}
	        }

        }

        return $res;

	}



}