<?php 

	function p($arg){
		echo "<pre>";
		print_r($arg);
		echo "</pre>";
		print_r(get_included_files());
	}


	function duration_format($str){
		if(false === strpos($str,':')){
            $hour   = floor($str/3600);
            $hour   = $hour<10?'0'.$hour:$hour;
            $minute = floor($str/60);
            $minute   = $minute<10?'0'.$minute:$minute;
            $second = round($str%60);
            $second   = $second<10?'0'.$second:$second;
            $time = $hour.':'.$minute.':'.$second;
            return $time;
		}
		if(false !== strpos($str,':') && strlen($str)<8){
            $arr = explode(':',$str);
            $count = count($arr);
            switch ($count) {
            	case '1':
            		$res[0] = '00';
            		$res[1] = '00';
            		$res[2] = $arr[0]<10?'0'.$arr[0]:$arr[0];
            		break;
            	case '2':
            		$res[0] = '00';
            		$res[1] = $arr[0]<10?'0'.$arr[0]:$arr[0];
            		$res[2] = $arr[1]<10?'0'.$arr[1]:$arr[1];
            		break;
            	case '3':
            		$res[0] = $arr[0]<10?'0'.$arr[0]:$arr[0];
            		$res[1] = $arr[1]<10?'0'.$arr[1]:$arr[1];
            		$res[2] = $arr[2]<10?'0'.$arr[2]:$arr[2];
            		break;

            }
            $time = $res[0].':'.$res[1].':'.$res[2];
            return $time;
		}
		return $str;

	}
