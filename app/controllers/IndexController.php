<?php

use Phalcon\Mvc\Controller;



class IndexController extends Controller
{
   public function indexAction(){

      echo $this->curlHelper->get('https://www.baidu.com/');die;
   }

    public function testAction(){
        set_time_limit(0);
        $process = 100;//线程数
        $times = 150;//请求次数

        $position_arr = [
            'main_splash',
            'main_important',
            'main_new',
            'news_neiye',
            'main_attent',

        ];
        $platform_arr = [''];
        $position = $position_arr[array_rand($position_arr,1)];
        $platform = $platform_arr[array_rand($platform_arr,1)];
        // $url = 'http://118.178.129.124:80/allOne.php?ad_name=main_important_ios';
        $url = 'http://www.ad.cn/allOne.php?ad_name='.$position.$platform;
        $post_data = [
            'ip'           =>  '10.1.1.104',
            'version_code' =>  '4.6.5',
            'operator'     =>   46002,     // integer 设备的网络运营商 0-未知, 1-移动, 2-联通, 3-电信
            'isboot'       =>   $platform=='ios'?0:4,         //是否是APP启动
            'ts'           =>   1510539281,   //请求时间戳
            'devicetype'   =>   0,        // 0 phone 1 pad  4 android phone 5 android pad
            'adh'          =>   188,       //ad高度
            'vendor'       =>  'Apple',     //手机品牌
            'osv'          =>  '9.3.5',     //手机系统版本
            'dvh'          =>   1334 ,     // integer 屏幕高度， 以像素为单位
            'tag'          =>  "山东,同曦,辽宁,篮网,爵士,活塞,CBA,森林狼,篮球,NBA,步行者,self,公牛,凯尔特人,广东,热火,魔术,浙江,火箭,湖'人,鹈鹕,快船,骑士',北控,青岛,福建,国王,雷霆,尼克斯,中国女篮,太阳,76人,iOS4.6.5,山西,马刺,上海,全部,猛龙,天津,八一,美国男篮',吉林,掘金,灰熊',雄鹿,勇士,小牛,深圳,江苏,新疆,重庆,奇才,黄蜂,中国男篮,四川,老鹰,开拓者,北京,广州,广厦",
            'os'           =>  'iOS',     // string 应用包名（例如， com.foo.mygame) 安卓: package name, 苹果: bundleID
            'lan'          =>  'zh-Hans-CN',  //语言
            'adw'          =>   718 ,     //ad宽度
            'geo'          =>  '',         //手机定位 (经,纬)
            'net'          =>   2 ,       //网络 2表示WiFi 1表示手机蜂窝网络
            'openudid'     =>  '4033bdf06b747ae133328936821078500ad2ff43',
            'platform'     =>  'ios',      //手机平台(android或者apple)
            'orientation'  =>   0 ,       //integer 用户设备的屏幕方向 1：竖屏；2：横屏；3：未知；
            'dvw'          =>   750,      // integer 屏幕宽度，以像素为单位
            'appname'      =>  'zhibo8',   // string Application name
            'model'        =>  'iPhone7,2',   //手机型号
            'density'      =>   326,
            '_only_care'   =>   1 ,       //1篮球2足球  默认全部
            'device'       =>  'iPhone 6',    //手机平台(android或者apple)
            'idfa'         =>  '6C53AE7D-176F-4946-B87D-8086C35FA9C7',   // string: required 用户设备唯一标识 对于 IOS 设备，该值为 'idfa 对于 android 设备，该值为 imei
            'imei'         =>  '863984020551652',   // string: required 用户设备唯一标识 对于 IOS 设备，该值为 'idfa 对于 android 设备，该值为 imei
        ];


        // for($i=0;$i<$times;$i++){
        //     echo "<pre>";       
        //     print_r($this->curl_http($url, $post_data,$process,10));


        // }
        print_r($this->post($url, $post_data,3));
        echo 'done';die;




    }

 


    public function curl_http($url, $post_data,$all,$timeout=30){
        $res = array();
        $mh = curl_multi_init();//创建多个curl语柄
        for($k=0;$k<$all;$k++){
            $conn[$k]=curl_init($url);
            curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout);//设置超时时间
            curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            // curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7);//HTTp定向级别
            curl_setopt($conn[$k], CURLOPT_HEADER, 0);//这里不要header，加块效率
            curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
            curl_setopt($conn[$k], CURLOPT_POST, 1);
            curl_setopt($conn[$k], CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($conn[$k],CURLOPT_RETURNTRANSFER,1);
            curl_multi_add_handle ($mh,$conn[$k]);
        }
        
        // 执行批处理句柄
        $active = null;
        do{
            $mrc = curl_multi_exec($mh,$active);//当无数据，active=true
        }while($mrc == CURLM_CALL_MULTI_PERFORM);//当正在接受数据时
        while($active && $mrc == CURLM_OK){//当无数据时或请求暂停时，active=true
           // if(curl_multi_select($mh) != -1){
                do{
                    $mrc = curl_multi_exec($mh, $active);
                }while($mrc == CURLM_CALL_MULTI_PERFORM);
           // }
        }

        for($k=0;$k<$all;$k++){
            curl_error($conn[$k]);
            $res[$k]=curl_multi_getcontent($conn[$k]);//获得返回信息
            // $res[$k]=curl_getinfo($conn[$k]);//返回头信息
            curl_close($conn[$k]);//关闭语柄
            curl_multi_remove_handle($mh  , $conn[$k]);//释放资源  
        }

        curl_multi_close($mh);
        return $res;
    }
   


    public function post($url, $post_data,$time_out=3){

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

        // curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;

    }



}
