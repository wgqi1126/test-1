<?php
/**
 * Created by PhpStorm.
 * User: yuanmingzhao
 * Date: 2017/1/21
 * Time: 下午9:30
 */

date_default_timezone_set('Asia/Shanghai');
//error_reporting(E_ALL);
$ad_name = empty($_REQUEST['ad_name'])?'':$_REQUEST['ad_name'];
$platform = empty($_REQUEST['platform'])?'android':$_REQUEST['platform'];
$version_android = empty($_REQUEST['version_name'])?'':$_REQUEST['version_name'];
$version_ios = empty($_REQUEST['version_code'])?'':$_REQUEST['version_code'];
$version_code = $version_ios;
$version_android_val = (int)str_ireplace('.','',$version_android);
$version_ios_val = (int)str_ireplace('.','',$version_ios);


$only_care = empty($_REQUEST['_only_care'])?0:(int)$_REQUEST['_only_care'];


$is_wifi = !empty($_REQUEST['net'])&&$_REQUEST['net']==2?true:false;



$redis = new Redis();
$redis->pconnect('46cb88787eb24653.m.cnhza.kvstore.aliyuncs.com',6379);
if ($redis->auth('46cb88787eb24653:wuU098SH2aslk') == false) {
    //die($redis->getLastError());
}else{
    $key = 'ad_'.date("Y-m-d").'_'.md5('api_'.$ad_name);
    $ret = $redis->incr($key);
}

$black_words = array();
$black_domain = array();
$black_img_url = array();
if(stripos($ad_name,'splash')!==false){
    $black_words = $redis->sMembers('black_words_splash');
    $black_domain = $redis->sMembers('black_domain_splash');
    $black_img_url = $redis->sMembers('black_img_url_splash');
}else{
    $black_words = $redis->sMembers('black_words');
    $black_domain = $redis->sMembers('black_domain');
    $black_img_url = $redis->sMembers('black_img_url');
}


if($platform == 'android' && stristr($ad_name,'_android')===false){
    $ad_name = $ad_name.'_android';
}elseif ($platform == 'ios' && stristr($ad_name,'_ios')===false){
    $ad_name = $ad_name.'_ios';
}
if($ad_name=='neiye_video_nba_android'||$ad_name=='neiye_video_zuqiu_android'||$ad_name=='neiye_news_nba_android'||$ad_name=='neiye_news_zuqiu_android'){
    $ad_name = 'news_neiye_android';
}elseif ($ad_name=='neiye_video_nba_ios'||$ad_name=='neiye_video_zuqiu_ios'||$ad_name=='neiye_news_nba_ios'||$ad_name=='neiye_news_zuqiu_ios'){
    $ad_name = 'news_neiye_ios';
}elseif($ad_name=='neiye_news'||$ad_name=='neiye_news_android'||$ad_name=='neiye_news_ios'){//针对调试模式
    $ad_name = 'news_neiye';
}


$result_arr = array();

if(!empty($_REQUEST['_debug']) && ($_REQUEST['_debug']==3 || $_REQUEST['_debug']==4)){
    $ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}

if(!empty($_REQUEST['_debug']) && $_REQUEST['_debug']==1){
    $ad_arr = getBaiduAdvert($platform,$ad_name);
    $ad_arr['spare'] = getSelfAdvert($platform,$ad_name,$only_care,$redis);


    if(!empty($_REQUEST['idfa']) && ($_REQUEST['idfa']=='B01D34D4-D06C-4125-B3C2-4381E968ACD0' || $_REQUEST['idfa']=='D2DB683B-3D4E-4572-9047-65459C263F39'  || $_REQUEST['idfa']=='6C53AE7D-176F-4946-B87D-8086C35FA9C7' || $_REQUEST['idfa']=='6C53AE7D-176F-4946-B87D-8086C35FA9C7' )){
        $ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
        $result_arr[] = $ad_arr;
        echo json_encode($result_arr);
        exit();
    }
    if(!empty($_REQUEST['imei']) && ($_REQUEST['imei']=='863984020551652' || $_REQUEST['imei']=='862470031392790'  || $_REQUEST['imei']=='862470031392782'  || $_REQUEST['imei']=='863151023906321'  || $_REQUEST['imei']=='860716030428597'  || $_REQUEST['imei']=='860716030975643'|| $_REQUEST['imei']=='866960021541667'  || $_REQUEST['imei']=='866960021534506')){
        $ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
        $result_arr[] = $ad_arr;
        echo json_encode($result_arr);
        exit();
    }

    if(stripos($ad_name,'live_popup')!==false){
        $ad_arr = getTestAdvert($platform,$ad_name,$redis);
    }

//    $ad_arr = getSelfAdvert($platform,$ad_name,$only_care,$redis);
//    $ad_arr['img']='https://img.alicdn.com/tps/TB1QMmvPVXXXXcxaXXXXXXXXXXX-520-280.jpg_.webp';
//    $ad_arr['deeplink']['link'] = 'tenvideo2://?action=1&video_id=p01678icoxq';
//    $ad_arr['url'] = 'http://v.youku.com/v_show/id_XMjYxMzYzODg0NA==.html';


    if(stripos($ad_name,'main_splash')!==false || stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false || stripos($ad_name,'signal_list')!==false){
        $ad_arr = getTestAdvert($platform,$ad_name,$redis);
    }

    //iphone se idfa	3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27
    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa']=='3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
        if(stripos($ad_name,'main_important')!==false){
            $ad_arr = getFillAdvert($platform,$ad_name,$redis);
        }
        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
        }
    }

    //巨鲨技术 1DE79117-C9D5-4633-934C-675581610BDD
    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa']=='1DE79117-C9D5-4633-934C-675581610BDD'){
        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
        }
    }

    if(stripos($ad_name,'splash')){
        $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
        $ad_arr['img'] = 'http://ggtu.qiumibao.com/2017/10/09/59db4efb40a0a.jpg';
    }


    if(stripos($ad_name,'zhibo_nav')!==false){
        $ad_arr = getZhiboNav($platform,$ad_name,$redis);
    }

    if(stripos($ad_name,'room')!==false){
        $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
    }

    $ad_arr = getJingDongAdvert($platform,$ad_name,$redis);
    //$ad_arr = getJinRiTouTiaoAdvert($platform,$ad_name,$redis);

    if(!empty($_REQUEST['idfa']) && stripos($ad_name,'main_splash')!==false){
        $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
    }


    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}
//iphone se idfa	3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27
//S7 iem	355905071724620

$self_arr = getSelfAdvert($platform,$ad_name,$only_care,$redis);
//独占广告直接展示
if($self_arr['monopolize']=='enable'){
//    if($platform=='ios' && $version_ios_val>=459 && (stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'video_list')!==false) ){
//
//    }else{
        $result_arr[] = $self_arr;

    ///临时加第十条为DSP  --start
//    if(stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false){
//        $dsp_advert = false;
//        $dsp_weight = $redis->hGetAll('dsp_weight');
//        if(!empty($dsp_weight)){
//            $ad_shop_arr = array(
//                array('name'=>'maiguan','weight'=>$dsp_weight[$platform.'_list_maiguan']),
//                array('name'=>'xunfei','weight'=>$dsp_weight[$platform.'_list_xunfei']),
//                array('name'=>'inmobi','weight'=>$dsp_weight[$platform.'_list_inmobi']),
//            );
//            $dsp_advert = advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis);
//            if(empty($dsp_advert)){
//                $dsp_advert = getSelfAdvert($platform,$ad_name,$only_care,$redis,true);
//            }
//
//            if(!empty($dsp_advert)){
//                $dsp_advert['show_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_bu&t='.time();
//                $dsp_advert['click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_buClick'.'&t='.time();
//                $dsp_advert['position'] = 9;
//                $result_arr[] = $dsp_advert;
//            }
//        }
//    }
    ///临时加第十条为DSP  --end


    $shanghai_advert = getShanghaiAdvert($platform,$ad_name,$redis);
    if($shanghai_advert!==false && $shanghai_advert['position']==0){
        $result_arr[] = $shanghai_advert;
    }


    ///临时加第十条  --start
    if(stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false){
        $dsp_advert = getFillAdvert($platform,$ad_name,$redis);
        if(!empty($dsp_advert)){
            $dsp_advert['show_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_bu&t='.time();
            $dsp_advert['click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_buClick'.'&t='.time();
            $dsp_advert['position'] = 9;
            $result_arr[] = $dsp_advert;
        }
    }
    ///临时加第十条  --end


        echo json_encode($result_arr);
        exit();
//    }
}

$dsp_weight = $redis->hGetAll('dsp_weight');


//开屏广告
if( empty($_REQUEST['_dev']) && stripos($ad_name,'splash')!==false ){
//    if(!empty($_REQUEST['imei']) && ($_REQUEST['imei']=='862534036087075' )){
//        $ad_arr = getDuomengAdvert($platform,$ad_name,$redis);
//        $result_arr[] = $ad_arr;
//        echo json_encode($result_arr);
//        exit();
//    }

    $ad_shop_arr = array();
    if( $platform == 'android'){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>1),
            array('name'=>'xunfei','weight'=>1),
        );
    }else{
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>2),
            array('name'=>'xunfei','weight'=>2),
            array('name'=>'inmobi','weight'=>1),
        );
    }

    if(!empty($dsp_weight)){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>$dsp_weight[$platform.'_splash_maiguan']),
            array('name'=>'xunfei','weight'=>$dsp_weight[$platform.'_splash_xunfei']),
            array('name'=>'inmobi','weight'=>$dsp_weight[$platform.'_splash_inmobi']),
            array('name'=>'kuaiyou','weight'=>$dsp_weight[$platform.'_splash_kuaiyou']),
            array('name'=>'jusha','weight'=>$dsp_weight[$platform.'_splash_jusha']),
            array('name'=>'toutiao','weight'=>$dsp_weight[$platform.'_splash_toutiao']),
            array('name'=>'jdjr','weight'=>$dsp_weight[$platform.'_splash_jdjr']),
        );
    }

    $ad_arr = advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis);

    //对量测试广告-start
    if($ad_arr===false ){
//        $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
//        $ad_arr = getJinRiTouTiaoAdvert($platform,$ad_name,$redis);
//        $ad_arr = getJingDongAdvert($platform,$ad_name,$redis);
    }
    //对量测试广告-end

    if($ad_arr===false){
        $ad_arr = $self_arr;
    }


    //巨鲨技术 1DE79117-C9D5-4633-934C-675581610BDD
    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa']=='1DE79117-C9D5-4633-934C-675581610BDD'){
        $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
    }

    //快友技术 863254033698308
    if(!empty($_REQUEST['imei']) && $_REQUEST['imei']=='863254033698308'){
        $ad_arr = getKuaiyouAdvert($platform,$ad_name,$redis);
    }

    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}

if(stripos($ad_name,'zhibo_nav')!==false){
    $ad_arr = getZhiboNav($platform,$ad_name,$redis);
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}


if(stripos($ad_name,'signal_list')!==false){
    $ad_arr = getSignalAdvert($platform,$ad_name,$redis);
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}

if(stripos($ad_name,'room')!==false){
    $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}

if(stripos($ad_name,'result')!==false){
    $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}

//非内页-信息流
if( empty($_REQUEST['_dev']) && stripos($ad_name,'neiye')===false ){
    $ad_shop_arr = array(
        array('name'=>'maiguan','weight'=>2),
        array('name'=>'xunfei','weight'=>2),
        array('name'=>'inmobi','weight'=>1),
        //array('name'=>'xinshu','weight'=>1),
    );
    if( $platform == 'android'){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>3),
            array('name'=>'xunfei','weight'=>3),
            array('name'=>'inmobi','weight'=>1),
        );
    }

    if(!empty($dsp_weight)){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>$dsp_weight[$platform.'_list_maiguan']),
            array('name'=>'xunfei','weight'=>$dsp_weight[$platform.'_list_xunfei']),
            array('name'=>'inmobi','weight'=>$dsp_weight[$platform.'_list_inmobi']),
            array('name'=>'kuaiyou','weight'=>$dsp_weight[$platform.'_list_kuaiyou']),
            array('name'=>'jusha','weight'=>$dsp_weight[$platform.'_list_jusha']),
            array('name'=>'toutiao','weight'=>$dsp_weight[$platform.'_list_toutiao']),
            array('name'=>'jdjr','weight'=>$dsp_weight[$platform.'_list_jdjr']),
        );
    }

    //自投广告独占模式
    $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
    $ad_arr = $ad_arr===false?advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis):$ad_arr;

    //填充广告-start
    if($ad_arr===false){
        //$ad_arr = getFillAdvert($platform,$ad_name,$redis);
//        if(stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false){
//            $ad_arr = getMoyichengAdvert($platform,$ad_name,$redis);
//        }
        //$ad_arr = getJinRiTouTiaoAdvert($platform,$ad_name,$redis);
//        $ad_arr = getJingDongAdvert($platform,$ad_name,$redis);
    }
    //填充广告-end

    $ad_arr = $ad_arr===false?$self_arr:$ad_arr;

    //自投广告跑余量模式
//    $ad_arr = advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis);
//    if($ad_arr===false){
//        $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
//        $ad_arr = $ad_arr===false?$self_arr:$ad_arr;
//    }

    //测试设备
//    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
//        $ad_arr = getTestAdvert($platform,$ad_name,$redis);
//    }
//    if(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'){
//        $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);
//    }

    $result_arr[] = $ad_arr;


    ///临时加第十条  --start
    if(stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false){
        if(!empty($ad_arr)){
//            $dsp_advert = advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis);
//            //$dsp_advert = $dsp_advert===false?$self_arr:$dsp_advert;//dsp没有广告 再附加自投广告
            $dsp_advert = getFillAdvert($platform,$ad_name,$redis);
            if(!empty($dsp_advert)){
                $dsp_advert['show_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_bu&t='.time();
                $dsp_advert['click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_buClick'.'&t='.time();
                $dsp_advert['position'] = 9;
                $result_arr[] = $dsp_advert;
            }
        }
    }

    ///临时加第十条为DSP  --end

    echo json_encode($result_arr);
    exit();
}



//内页
if(empty($_REQUEST['_dev']) && stripos($ad_name,'neiye')!==false){
    $ad_shop_arr = array();
    if( $platform == 'android'){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>1),
        );
    }else{
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>1),
            array('name'=>'inmobi','weight'=>1),
        );
    }
    if(!empty($dsp_weight)){
        $ad_shop_arr = array(
            array('name'=>'maiguan','weight'=>$dsp_weight[$platform.'_neiye_maiguan']),
            array('name'=>'xunfei','weight'=>$dsp_weight[$platform.'_neiye_xunfei']),
            array('name'=>'inmobi','weight'=>$dsp_weight[$platform.'_neiye_inmobi']),
            array('name'=>'kuaiyou','weight'=>$dsp_weight[$platform.'_neiye_kuaiyou']),
            array('name'=>'toutiao','weight'=>$dsp_weight[$platform.'_neiye_toutiao']),
            array('name'=>'jdjr','weight'=>$dsp_weight[$platform.'_neiye_jdjr']),
        );
    }

    $ad_arr = getShanghaiAdvert($platform,$ad_name,$redis);

    //临时跑填充 --start
    if($ad_arr===false){
        //$ad_arr = getMoyichengAdvert($platform,$ad_name,$redis);
    }
    //临时跑填充  --end

    $ad_arr = $ad_arr===false?advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis):$ad_arr;

    if($ad_arr===false){
        $android_status_ok = $platform=='android' && $version_code>=94;
        $ios_status_ok = $platform=='ios' && $version_ios_val>=445;
        if($ios_status_ok){//$android_status_ok ||
            $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
            $ad_arr['show_desc'] = false;
            $ad_arr['show_symbol'] = false;
            $ad_arr['title_length'] = 32;
            if(!empty($self_arr)){
                $ad_arr['spare'] = $self_arr;
            }
        }
    }

    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}




//非内页（包含开屏）
if(empty($_REQUEST['_dev']) && (stripos($ad_name,'splash')!==false || stripos($ad_name,'neiye')===false)){
    $ad_arr = getXunfeiAdvert($platform,$ad_name,$redis);
    if($ad_arr===false){
        $ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
    }

    //if((stripos($ad_name,'list')!==false ) && $ad_arr===false && $platform == 'ios'){
    if($ad_arr===false && $platform == 'ios'){
        $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
    }

//    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa']=='3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
//        if($ad_arr===false && stripos($ad_name,'splash')===false && $platform == 'ios'){
//            $ad_key = 'xinshu';
//            $ad_count = $redis->get('ad_'.md5($ad_key));
//            if($ad_count<10000){
//                $ad_arr = getXinshuAdvert($platform,$ad_name,$redis);
//                if(!empty($ad_arr)){
//                    $ad_arr['name'] .= '_show'.$ad_count;
//                }
//            }
//        }
//    }

    //$_REQUEST['imei']=='355905071724620' //三星
    if($ad_arr===false && $platform=='android' && stripos($ad_name,'splash')!==false){
        $ad_key = 'inmobi20170510splash';
        $ad_count = $redis->get('ad_'.md5($ad_key));
        if($ad_count<10000){
            $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
            if(!empty($ad_arr)){
                $ad_arr['name'] .= '_show'.$ad_count;
            }
        }
    }

//    if(!empty($_REQUEST['imei']) && $_REQUEST['imei']=='355905071724620' ){
//        $ad_shop_arr = array(
//            array('name'=>'maiguan','weight'=>1),
//            array('name'=>'xunfei','weight'=>1),
//            array('name'=>'inmobi','weight'=>1),
//        );
//        $ad_arr = advert_echo($ad_shop_arr,array(),$platform,$ad_name,$redis);
//        if($ad_arr!==false){
//            $ad_arr['id'] = 99999;
//        }
//    }

//    if(!empty($_REQUEST['imei']) && ( $_REQUEST['imei']=='355905071724620' || $_REQUEST['imei']=='862298031887877' || $_REQUEST['imei']=='862298031887885')){
//        $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
//        $result_arr[] = $ad_arr;
//        echo json_encode($result_arr);
//        exit();
//    }


    if($ad_arr===false){
        $ad_arr = $self_arr;//getSelfAdvert($platform,$ad_name,$only_care,$redis);
    }
    $result_arr[] = $ad_arr;
    echo json_encode($result_arr);
    exit();
}



//内页的
$ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);

if($ad_arr===false && $platform == 'ios' && stripos($ad_name,'neiye')!==false){
    $ad_key = 'changsi2';
    $ad_count = $redis->get('ad_'.md5($ad_key));
    if($ad_count<200000){
        $ad_arr = getChangsiBannerAdvert($platform,$ad_name);
        if(!empty($ad_arr)){
            $ad_arr['name'] .= '_show'.$ad_count;
        }
    }
}

if($ad_arr===false){
    if($platform == 'android'){
        $ad_arr = stripos($ad_name,'neiye')!==false?$ad_arr:getXunfeiAdvert($platform,$ad_name,$redis);
        if($ad_arr===false && $version_code>=94 && stripos($ad_name,'neiye')!==false ){
            //$ad_arr = $version_code>=96&&mt_rand(1,2)==1?getBaiduAdvert($platform,$ad_name):getGuangdiantongAdvert($platform,$ad_name);
            $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
            $ad_arr['show_desc'] = false;
            $ad_arr['show_symbol'] = false;
            $ad_arr['title_length'] = 32;
            $spare = $self_arr;//getSelfAdvert($platform,$ad_name,$only_care,$redis);
            if(!empty($spare)){
                $ad_arr['spare'] = $spare;
            }
            //$ad_arr['spare'] = getSelfAdvert($platform,$ad_name,$only_care,$redis);
        }
//elseif($ad_arr===false && stripos($ad_name,'splash')===false && $version_code>=95){
//            $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
//            $ad_arr['show_desc'] = false;
//            $ad_arr['show_symbol'] = false;
//            $ad_arr['title_length'] = 30;
//            $ad_arr['spare'] = getSelfAdvert($platform,$ad_name,$only_care,$redis);
//        }
    }else{

        if($version_ios_val==444 && stripos($ad_name,'neiye')!==false ){
            if(stripos($_REQUEST['ad_name'],'neiye_video')===false){
                $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
                $ad_arr['show_desc'] = false;
                $ad_arr['show_symbol'] = false;
                $ad_arr['title_length'] = 32;
                $spare = $self_arr;//getSelfAdvert($platform,$ad_name,$only_care,$redis);
                if(!empty($spare)){
                    $ad_arr['spare'] = $spare;
                }
            }
        }elseif ($version_ios_val>=445 && stripos($ad_name,'neiye')!==false){
                //$ad_arr = $version_ios_val>=450&&mt_rand(1,2)==1?getBaiduAdvert($platform,$ad_name):getGuangdiantongAdvert($platform,$ad_name);
                $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
                $ad_arr['show_desc'] = false;
                $ad_arr['show_symbol'] = false;
                $ad_arr['title_length'] = 32;
                $spare = $self_arr;//getSelfAdvert($platform,$ad_name,$only_care,$redis);
                if(!empty($spare)){
                    $ad_arr['spare'] = $spare;
                }
        }else{
//            if(stripos($_REQUEST['ad_name'],'splash')!==false){
//                $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
//            }
            $ad_arr = stripos($ad_name,'neiye')!==false?$ad_arr:getXunfeiAdvert($platform,$ad_name,$redis);
        }
    }
}

if($ad_arr===false){
    $ad_arr = $self_arr;
}


if($ad_arr===false && stripos($ad_name,'splash')!==false && $platform != 'android'){
    $ad_arr = array(
        'name'=>'补iOS空白',
        'status'=>'enable',
        'platform'=>'',
        'placement'=>'开机画面',
        'type'=>'图片',
        'showTimes'=>9999,
        'duration'=>1,
        'img'=>'http://ggtu.qiumibao.com/sucai/blank.png',
        'module'=>'',
        'content'=>'',
        'url'=>'',
        'position'=>3,
        'act'=>'web',
        'ua_ping_urls'=>array(),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'click_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(),
    );
}

//$ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
//if($ad_arr===false){
//    $ad_arr = getXunfeiAdvert($platform,$ad_name);
//    if($ad_arr===false){
//        //$ad_arr = getSelfAdvert($platform,$ad_name,$only_care,$redis);
//        $ad_arr = getGuangdiantongAdvert($platform,$ad_name,$only_care,$redis);
//    }
//}


if(($platform=='android' || $platform=='ios') && !empty($_REQUEST['_dev'])){
    $ad_arr = getGuangdiantongAdvert($platform,$ad_name);
    $ad_arr['show_desc'] = false;
    $ad_arr['show_symbol'] = true;
    $ad_arr['title_length'] = 12;
    $ad_arr['spare'] = array(
        'name'=>'补iOS空白',
        'status'=>'enable',
        'platform'=>'',
        'placement'=>'开机画面',
        'type'=>'txt_img',
        'showTimes'=>9999,
        'duration'=>1,
        'img'=>'http://static4style.qiumibao.com/common/img/logo_ny_2.gif',
        'module'=>'',
        'content'=>'补空白',
        'url'=>'http://static4style.qiumibao.com/common/img/logo_ny_2.gif',
        'position'=>3,
        'act'=>'web',
        'ua_ping_urls'=>array('http://www.zhibo8.cc/images/2weima_116.png?_t='.time()),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),  
        'click_ping_urls'=>array(),
        'ua_click_ping_urls'=>array('http://tu.qiumibao.com/ico/tub.png?_t='.time()),
    );
}


if($platform=='ios' && !empty($_REQUEST['_dev'])) {
    $ad_arr = getXunfeiAdvert($platform, $ad_name,$redis);
}


$result_arr[] = $ad_arr;
echo json_encode($result_arr);



function getSelfAdvert($platform,$ad_name,$only_care,$redis,$is_except_monopolize=false){
    $tag = empty($_REQUEST['tag'])?'':$_REQUEST['tag'];
    $label_arr = explode(',',$tag);

    $care_type = 'all';
    if($only_care==1){
        $care_type = 'nba';
    }elseif($only_care==2){
        $care_type = 'zuqiu';
    }

    $data = $redis->sMembers('set_'.$ad_name);

    $advert_arr_with_label = array();
    $advert_arr_with_care = array();
    $advert_arr_with_fore = array();
    foreach ($data as $advert_str){
        $advert = json_decode($advert_str,true);
        $ad_label_arr = explode(',',$advert['label']);
        if(!empty(array_intersect($label_arr,$ad_label_arr)) || stripos($advert['label'],'全部标签')!==false){
            $advert_arr_with_label[] = $advert;
        }
//    if(in_array($advert['label'],$label_arr)){
//        $advert_arr_with_label[] = $advert;
//    }
        if($care_type==$advert['match_type'] || $care_type=='all' || $advert['match_type']=='all'){
            if($is_except_monopolize && $advert['monopolize']=='enable'){

            }else{
                $advert_arr_with_care[] = $advert;
            }
        }

        //force_care
        if($care_type==$advert['force_care'] || $advert['force_care']=='all'){
            if($is_except_monopolize && $advert['monopolize']=='enable'){

            }else {
                $advert_arr_with_fore[] = $advert;
            }
        }

        //独占广告
        if($advert['monopolize']=='enable'){
            $advert_arr_with_monopolize[] = $advert;
        }
    }


    $advert_arr = empty($advert_arr_with_label)?$advert_arr_with_care:$advert_arr_with_label;
    if(!empty($advert_arr_with_fore)){
        $advert_arr = $advert_arr_with_fore;
    }
    if(!empty($advert_arr_with_monopolize) && !$is_except_monopolize){
        $advert_arr = $advert_arr_with_monopolize;
    }

    $data = empty($advert_arr)?array():$advert_arr[array_rand($advert_arr,1)];

    if(!empty($data) && $data['id']==1){
        return $data;
    }



    if(!empty($data['img'])){
        $data['content'] = limitContent($data['content'],$ad_name);
        $result_arr = array();
        $ad_arr = array(
            'id'=>$data['id'],
            'name'=>$data['name'].$ad_name,
            'type'=>'txt_img',
            'img'=>$data['img'],
            'content'=>$data['content'],
            'url'=>$data['url'],
            'showTimes' => $data['showTimes'],
            'duration' => $data['duration'],
            'position'=>4,
            'group'=>0,
            'act'=>'web',
            'ua_ping_urls'=>array(
                'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&t='.time(),
            ),
            'down_ping_urls'=>array(),
            'show_ping_urls'=>array(),
            'ua_click_ping_urls'=>array(
                'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&t='.time()
            ),
            'click_ping_urls'=>array(),
            'monopolize'=>$data['monopolize'],
        );
        if(!empty($data['ua_ping_url'])){
            $ad_arr['ua_ping_urls'][] = $data['ua_ping_url'].'&_t='.time();
        }
        if(!empty($data['ua_click_ping_url'])){
            $ad_arr['ua_click_ping_urls'][] = $data['ua_click_ping_url'].'&_t='.time();
        }

        if($data['id']==535 || $data['id']==536){
            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_lswzkp&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_lswzkpClick'.'&t='.time();
        }


        if($data['type']=='banner'){
            $ad_arr['type'] = 'banner_img';
            $ad_arr['ratio'] = '6:1';
        }

        if(stripos($ad_name,'_neiye_')!==false){
            $ad_arr['type'] = 'banner_img';
            $ad_arr['ratio'] = '4:1';
        }elseif(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = $data['module']=='none'?'':$data['module'];
        }


        if(stripos($ad_name,'main_splash')!==false && $platform!='ios' && (!empty($_REQUEST['version_name']) && $_REQUEST['version_name']=='4.6.9')){
            $ad_arr['img'] .= stripos($ad_arr['img'],'?')!==false?'&_t='.time():'?_t='.time();
            $ad_arr['down_ping_urls'] = $ad_arr['ua_ping_urls'];
            $ad_arr['ua_ping_urls'] = array();
        }

        if($platform=='ios' && (!empty($_REQUEST['version_code']) && $_REQUEST['version_code']=='4.5.8') && $ad_arr['type'] == 'banner_img'){
            return false;
        }

        return $ad_arr;
    }else{
        return false;
    }
}

//function getTestAdvert($platform,$ad_name,$redis=null){
//    $ad_arr = array(
//        'id'=>1,
//        'name'=>'test'.$ad_name,
//        'type'=>'txt_img',
//        'img'=>'http://ggtu.qiumibao.com/ad/zongbu/caipiao.jpg',
//        'content'=>'测试内容',
//        'url'=>'https://www.zhibo8.cc/zhibo/zuqiu/2017/0724huangjiamadelivsmanlian.htm',
//        'showTimes' => 999,
//        'duration' => 3,
//        'position'=>4,
//        'group'=>0,
//        'act'=>'web',
//        'ua_ping_urls'=>array(
//            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&t='.time(),
//        ),
//        'down_ping_urls'=>array(),
//        'show_ping_urls'=>array(),
//        'ua_click_ping_urls'=>array(
//            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&t='.time()
//        ),
//        'click_ping_urls'=>array(),
//        'monopolize'=>'',
//    );
//
//    if(stripos($ad_name,'live_popup')!==false){
//        $ad_arr['type'] = 'banner_img';
//        $ad_arr['ratio'] = '4:1';
//    }elseif(stripos($ad_name,'main_splash')!==false){
//        $ad_arr['type'] = '图片';
//        $ad_arr['placement'] = '开机画面';
//        $ad_arr['module'] = '';
//    }
//
//    return $ad_arr;
//}


function getMaiguanAdvert($platform,$ad_name,$redis=null){
//    if(isOverByShowCount($platform,$ad_name,'maiguan',$redis)){
//        return false;
//    }
    addRequestCount($platform,$ad_name,'maiguan',$redis);

    $clientKey = '3D41E556D6F1562CDF1B0F717D6256C5';
//    $api_url = 'http://a.m86.mobi/api.htm';
    $api_url = 'http://a.mplusmedia.cn/api.htm';

    $aid = '200067';

    if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $aid = $platform=='android'?'200077':'212099';
    }elseif (stripos($ad_name,'main_news')!==false){
        $aid = $platform=='android'?'200077':'201085';
    }elseif (stripos($ad_name,'news_list')!==false){
        $aid = $platform=='android'?'201083':'201085';
    }elseif (stripos($ad_name,'video_list')!==false){
        $aid = $platform=='android'?'200081':'201086';
    }elseif (stripos($ad_name,'news_neiye')!==false){
        $aid = $platform=='android'?'200078':'201084';//$aid = '200067';
    }elseif (stripos($ad_name,'news_focus')!==false){
        $aid = '200070';
    }elseif (stripos($ad_name,'main_splash')!==false){
        $aid = $platform=='android'?'200080':'201087';
    }

//    if($platform != 'android' ){
//        if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '00000000-0000-0000-0000-000000000000'){
//            $redis->hSet('maiguan_err',time(),json_encode($_REQUEST));
//        }
//    }

//    if($aid=='200067'){
//        $redis->hSet('maiguan_err',time(),json_encode($_REQUEST));
//    }

    $clid = 'A565752CCB1006335B49EBC35E1E4625';

    $lyt = 3;

    $bn = $_REQUEST['vendor'];
    $mn = $_REQUEST['model'];
//0 Other
//1 Android
//2 IOS
//3 WindowsPhone
    $ost = $platform == 'android'?1:2;
    $osv = $_REQUEST['osv'];
    $rs = $_REQUEST['dvw'].','.$_REQUEST['dvh'];
    $rs = str_ireplace(',','x',$rs);
    $ut = $platform=='android'?'imei':'idfa';

    $anm = urlencode('直播吧');
    $pnm = $platform=='android'?'android.zhibo8':'com.zhibo8.client81136870';
    $idfa = empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei'])?'':$_REQUEST['imei'];
    $anid = empty($_REQUEST['adid'])?'':$_REQUEST['adid'];
    $mac = empty($_REQUEST['mac'])?'':$_REQUEST['mac'];
    $ts = time();
    $net = $_REQUEST['net'];
    if($net==2){
        $net = 'wifi';//1;//wifi
    }else{
        $net = 'mobile';//2;//数据网络
    }


    $mimes =  urlencode('image/png,image/jpeg');

    $mnc = $_REQUEST['operator'];;
    $ip = getenv("REMOTE_ADDR");
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $ppi = $_REQUEST['density'];
    $dst = 3;
    $fmt = 'json';

    $vk = sha1($clientKey.'|'.$$ut.'|'.$ts);
    $acceptEncoding = 'q=1.0, identity';//Accept-Encoding:gzip, deflate, sdch
    $version = '3.0';

    $url = $api_url.'?aid='.$aid.'&clid='.$clid.'&bn='.$bn.'&mn='.urlencode($mn).'&ost='.$ost.'&osv='.$osv.'&rs='.urlencode($rs).'&ut='.$ut.'&ts='.$ts.'&idfa='.$idfa.'&imei='.$imei.'&anid='.$anid.'&mac='.$mac.'&net='.$net.'&mnc='.$mnc.'&ip='.$ip.'&ua='.urlencode($ua).'&fmt='.$fmt.'&ppi='.$ppi.'&dst='.$dst.'&pnm='.$pnm.'&anm='.$anm.'&lyt='.$lyt.'&mimes='.$mimes;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "vk: ".$vk,
            "version: ".$version,
            "Accept-Encoding: q=1.0, identity",
        )
    );
    $res = curl_exec($ch);

    $res_arr = json_decode($res,true);

    if(!empty($res_arr['status']) && $res_arr['status']=='success'){
        addFilledCount($platform,$ad_name,'maiguan',$redis);//统计 填充计数
        foreach ($res_arr['adpod'] as $ad){
            $img = '';
            $content = '';
            if($ad['type']=='fs'){
                $img = $ad['mtr'][0]['url'];
            }elseif ($ad['type']=='natv'){
                foreach ($ad['mtr'] as $mtr){
                    if($ad['lyt']==3 && $mtr['id']==2){
                        $img = $mtr['url'];
                    }elseif ($ad['lyt']==3  && $mtr['id']==11){
                        $content = $mtr['text'];
                        //$content = mb_strlen($content)>16 ?mb_substr($content,0,16).'..':$content;
//                        if(stripos($ad_name,'important')!==false || stripos($ad_name,'attention')!==false){
//                            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                                $content = Onens::g_length($content)>50?Onens::g_substr($content,50,true):$content;
//                            }else{
//                                $content = Onens::g_length($content)>30?Onens::g_substr($content,30,true):$content;
//                            }
//                        }
                        $content = limitContent($content,$ad_name);
                    }elseif ($ad['lyt']==6 && $mtr['id']==1){
                        $img = $mtr['url'];
                    }
                }
            }elseif($ad['type']=='ban'){
                $img = $ad['mtr'][0]['url'];
            }


            $ad_arr = array(
                'name'=>$ad_name.' maiguan',
                'status'=>'enable',
                'type'=>'txt_img',
                'showTimes'=>9999,
                'duration'=>3,
                'img'=>$img,//$ad['mtr'][0]['url'],//'http://tu.qiumibao.com/img/1226aliyunios.jpg',
                'content'=>$content,
                'url'=>$ad['cu'],
                'module'=>'',
                'position'=>4,
                'act'=>'web',
                'ua_ping_urls'=>array(),
                'down_ping_urls'=>array(),
                'show_ping_urls'=>array(),
                'ua_click_ping_urls'=>array(),
                'click_ping_urls'=>array(),
            );

            if($ad['type']=='ban'){
                $ad_arr['type'] = 'banner_img';
                $ad_arr['ratio'] = $ad['mtr'][0]['w'].':'.$ad['mtr'][0]['h'];
            }


            if(stripos($ad_name,'main_splash')!==false){
                $ad_arr['type'] = '图片';
                $ad_arr['placement'] = '开机画面';
                $ad_arr['module'] = '';
            }

            foreach ($ad['tracking'] as $ping_url){
                if($ping_url['et']==2){
                    //$ad_arr['ua_ping_urls'] = $ping_url['tku'];
                    $ad_arr['show_ping_urls'] = $ping_url['tku'];
                }elseif ($ping_url['et']==3){
                    $ad_arr['click_ping_urls'] = $ping_url['tku'];//ua_click_ping_urls
                }
            }

//            foreach ($ad_arr['ua_ping_urls'] as $k=>$value){
//                $ad_arr['ua_ping_urls'][$k] .= '&_t='.time();
//            }


            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=maiguan&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=maiguan&t='.time();

            $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'maiguan',$redis);


//            if(stripos($ad_name,'main_splash')!==false && $platform!='ios'){
//                $ad_arr['img'] .= stripos($ad_arr['img'],'?')!==false?'&_t='.time():'?_t='.time();
//                $ad_arr['down_ping_urls'] = $ad_arr['ua_ping_urls'];
//                //$ad_arr['down_ping_urls'] = $ad_arr['show_ping_urls'];
//                $ad_arr['ua_ping_urls'] = array();
//            }

            collectMaterial($ad_arr,$platform,$ad_name,'maiguan',$redis);

            //屏蔽关键字
            $ad_arr['shop'] = 'maiguan';
            if(Tools::isBadAdvert($ad_arr)){
                collectErrMaterial($ad_arr,$platform,$ad_name,'maiguan',$redis);
                return false;
            }
            return $ad_arr;
        }
        return false;
    }else{
        return false;
    }
}


function getXunfeiAdvert($platform,$ad_name,$redis=null){
//    if(isOverByShowCount($platform,$ad_name,'xunfei',$redis)){
//        return false;
//    }
    addRequestCount($platform,$ad_name,'xunfei',$redis);

//    if($_SERVER['HTTP_USER_AGENT']=='KeepAliveClient'){
//        $redis->hSet('xunfei_err',time(),json_encode($_SERVER));
//    }

    $adunitid = $platform=='android'?'64FAC0085FC8DC679F6BF49A1F98445D':'B926A1381CA1D3E62DE875FF0830BC81';//重要信息流
    if(stripos($ad_name,'main_splash') !== false){
        $adunitid = $platform=='android'?'10630B7308D30404DCFBBA3155FCD946':'119C55CA3F77646567B01528A2426CE3';//'94E5F12D9C06BCE0BFB460DE5D246E87';
    }elseif (stripos($ad_name,'neiye') !== false){
        $adunitid = $platform=='android'?'F001EF2178BFBFB7E82EF2AFC82BF44A':'86C1C1C0336C5A8F9F4119A80C6FEBDE';
    }elseif (stripos($ad_name,'main_news')!==false){
        $adunitid = $platform=='android'?'C9B9C90F15D8860E667C6AC8947A9819':'6031BEC45A9CC157F9DAA80FBAC7D4F7';
    }elseif (stripos($ad_name,'news_list')!==false){
        $adunitid = $platform=='android'?'C9B9C90F15D8860E667C6AC8947A9819':'6031BEC45A9CC157F9DAA80FBAC7D4F7';
    }elseif (stripos($ad_name,'video_list')!==false){
        $adunitid = $platform=='android'?'C9B9C90F15D8860E667C6AC8947A9819':'6031BEC45A9CC157F9DAA80FBAC7D4F7';
    }

//    if(!empty($_REQUEST['adid'])&&$_REQUEST['adid']=='659489b92e9641b'){
//        echo '$adunitid:'.$adunitid;exit();
//    }

    $geo = '';
    if(!empty($_REQUEST['geo'])){
        $geo = $_REQUEST['geo'];
    }

    $url = 'http://ws.voiceads.cn/ad/request';

    $post_arr = array(
        'adunitid'=>$adunitid,
        'batch_cnt'=>1,
        'tramaterialtype'=>'json',
        'devicetype'=>'',
        'os'=>$_REQUEST['os'],
        'osv'=>$_REQUEST['osv'],
        //'openudid'=>'',
        'adid'=>empty($_REQUEST['adid'])?'':$_REQUEST['adid'],
        'imei'=>empty($_REQUEST['imei'])?'':$_REQUEST['imei'],
        //'idfa'=>'',
        'mac'=>empty($_REQUEST['mac'])?'':$_REQUEST['mac'],
        'density'=>$_REQUEST['density'],
        'operator'=>empty($_REQUEST['operator'])?'46000':$_REQUEST['operator'],
        'net'=>$_REQUEST['net'],
        'ip'=>getenv("REMOTE_ADDR"),
        'ua'=>$_SERVER['HTTP_USER_AGENT'],
        'ts'=>$_SERVER['REQUEST_TIME_FLOAT'],//毫秒
        'adw'=>empty($_REQUEST['adw'])?300:(int)$_REQUEST['adw'],
        'adh'=>empty($_REQUEST['adh'])?200:(int)$_REQUEST['adh'],
        'dvw'=>$_REQUEST['dvw'],
        'dvh'=>$_REQUEST['dvh'],
        'orientation'=>$_REQUEST['orientation'],
        'vendor'=>$_REQUEST['vendor'],
        'model'=>$_REQUEST['model'],
        'lan'=>'zh-CN',
        'isboot'=>0,
        //'csinfo'=>empty($_REQUEST['csinfo'])?'':json_decode($_REQUEST['csinfo'],true),
        'appid'=>'579f039b',
        'appname'=>'直播吧',
        'pkgname'=>'android.zhibo8',
        'appver'=>!empty($_REQUEST['version_name'])?$_REQUEST['version_name']:'',
        //'devicetype'=>0,
        'geo'=>$geo,
        'adtype'=>'brand',
        'landing_type'=>2
    );

    if($platform!='android'){
    //if($post_arr['os']!='Android'){
        //unset($post_arr['csinfo']);
        $post_arr['openudid'] = $_REQUEST['openudid'];
        $post_arr['idfa'] = $_REQUEST['idfa'];
        $post_arr['appid'] = '57a00946';
        $post_arr['pkgname'] = 'com.zhibo8.client81136870';
        $post_arr['appver'] = !empty($_REQUEST['version_code'])?$_REQUEST['version_code']:'';
    }

    if($ad_name=='main_splash'){
        $post_arr['adw'] = 640;
        $post_arr['adh'] = 960;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_arr));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,2);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Chrome/38.0",
            "Connection: keep-alive",
            "Accept-Charset: utf-8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "X-protocol-ver: 2.0",
//        'Content-Type: application/json',
            //'Content-Length: ' . strlen($data_string)
        )
    );
    $res = curl_exec($ch);
    if((!empty($_REQUEST['_debug']) && $_REQUEST['_debug']==8) ){
        echo $res;exit();
    }
    $res_arr = json_decode($res,true);

    if(!empty($res_arr['batch_ma'])){
        addFilledCount($platform,$ad_name,'xunfei',$redis);//统计 填充计数
        foreach ($res_arr['batch_ma'] as $advert){
            //$title = empty($advert['sub_title'])?'':$advert['sub_title'];
            $title = empty($advert['title'])?'':$advert['title'];
            //$title = mb_strlen($title)>16 ?mb_substr($title,0,16).'..':$title;
//            if(stripos($ad_name,'important')!==false || stripos($ad_name,'attention')!==false){
//                if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                    $title = Onens::g_length($title)>50?Onens::g_substr($title,50,true):$title;
//                }else{
//                    $title = Onens::g_length($title)>24?Onens::g_substr($title,24,true):$title;
//                }
//            }
            $title = limitContent($title,$ad_name);
            $ad_arr = array(
                'name'=>'',
                'type'=>'txt_img',
                'img'=>$advert['image'],
                'content'=>$title,
                'url'=>$advert['landing_url'],
                'position'=>4,
                'act'=>'web',
                'ua_ping_urls'=>$advert['impr_url'],
                'down_ping_urls'=>array(),
                'show_ping_urls'=>array(),
                'ua_click_ping_urls'=>$advert['click_url'],//array(),
                'click_ping_urls'=>array(),
            );

            if(!empty($ad_name) && stripos($ad_name,'main_splash') !== false){
                $ad_arr = array(
                    'name'=>'测试'.$adunitid,
                    'status'=>'enable',
                    'platform'=>'',
                    'placement'=>'开机画面',
                    'type'=>'图片',
                    'showTimes'=>9999,
                    'duration'=>3,
                    'img'=>$advert['image'],
                    'module'=>'',
                    'content'=>'',
                    'url'=>$advert['landing_url'],
                    'position'=>3,
                    'act'=>'web',
                    'ua_ping_urls'=>$advert['impr_url'],
                    'down_ping_urls'=>array(),
                    'show_ping_urls'=>array(),
                    'click_ping_urls'=>array(),
                    'ua_click_ping_urls'=>$advert['click_url'],
                    //'post'=>$post_arr
                );
            }

            $ad_arr['name'] = $ad_name.' xunfei';

            if(!empty($ad_name) && !empty($ad_arr['ua_ping_urls'])){
                $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=xunfei&t='.time();
                $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=xunfei&t='.time();
                //$ad_arr['net'] = empty($_REQUEST['net'])?'':$network_connection_type.' '.$network_operator_type;
            }


            if(stripos($ad_name,'main_splash')!==false && $platform=='android'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82816079&cid=233275&mid=220984&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82816079&cid=233275&mid=220984&oid=32173&productType=1';
            }elseif(stripos($ad_name,'main_important')!==false && $platform=='android'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82802606&cid=233273&mid=220982&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82802606&cid=233273&mid=220982&oid=32173&productType=1&t=MUQ9MN22ZqVaO50urkHOAF4is%2B9%2BPb8H%2F20XVapNWRE%3D';
            }elseif((stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false) && $platform=='android'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82804707&cid=233271&mid=220980&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82804707&cid=233271&mid=220980&oid=32173&productType=1&t=MUQ9MN22ZqVaO50urkHOAF4is%2B9%2BPb8H%2F20XVapNWRE%3D';
            }elseif(stripos($ad_name,'main_splash')!==false && $platform=='ios'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82814611&cid=233272&mid=220981&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82814611&cid=233272&mid=220981&oid=32173&productType=1';
            }elseif(stripos($ad_name,'main_important')!==false && $platform=='ios'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82826055&cid=233265&mid=220979&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82826055&cid=233265&mid=220979&oid=32173&productType=1&t=MUQ9MN22ZqVaO50urkHOAF4is%2B9%2BPb8H%2F20XVapNWRE%3D';
            }elseif((stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false) && $platform=='ios'){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82804939&cid=233274&mid=220983&oid=32173&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82804939&cid=233274&mid=220983&oid=32173&productType=1&t=MUQ9MN22ZqVaO50urkHOAF4is%2B9%2BPb8H%2F20XVapNWRE%3D';
            }

//            if(stripos($ad_name,'main_splash')!==false && $platform!='ios'){
//                $ad_arr['img'] .= stripos($ad_arr['img'],'?')!==false?'&_t='.time():'?_t='.time();
//                $ad_arr['down_ping_urls'] = $ad_arr['ua_ping_urls'];
//                $ad_arr['ua_ping_urls'] = array();
//            }

            $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'xunfei',$redis);


            collectMaterial($ad_arr,$platform,$ad_name,'xunfei',$redis);

            //屏蔽关键字
            $ad_arr['shop'] = 'xunfei';
            if(Tools::isBadAdvert($ad_arr)){
//                if(stripos($ad_name,'main_splash')!==false){
//                    $redis->hSet('xunfei_err',time(),json_encode($ad_arr));
//                }
                collectErrMaterial($ad_arr,$platform,$ad_name,'xunfei',$redis);
                return false;
            }

//            if(stripos($ad_name,'main_splash')!==false){
//                $redis->hSet('xunfei_ok',time(),json_encode($ad_arr));
//            }

            return $ad_arr;
        }
        return false;
    }else{
        return false;
    }
}

function getInmobiIOSAdvert($platform,$ad_name='',$redis=null){
//    if(isOverByShowCount($platform,$ad_name,'inmobi',$redis)){
//        return false;
//    }
    addRequestCount($platform,$ad_name,'inmobi',$redis);

//    if($platform=='android' && stripos($ad_name,'neiye')===false && stripos($ad_name,'splash')===false){
//        $rnd = mt_rand(1,3);
//        if($rnd!=1){
//            return false;
//        }
//    }


    $bundle = '';
    if($platform=='ios'){
        $ad_id = '1480829503357';//'1477722688080';
        if(stripos($ad_name,'neiye')!==false){
            $ad_id = '1479455216557';//'1479455216557';
        }elseif (stripos($ad_name,'important')!==false){
            $ad_id = '1480829503357';//'1477722688080';
        }elseif (stripos($ad_name,'splash')!==false){
            $ad_id = '1478966338094';
        }
        $bundle = "com.zhibo8.client81136870";
    }else{
        $ad_id = '1467110277069';//1466881926004;
        $bundle = 'android.zhibo8';
        if(stripos($ad_name,'splash')!==false){
            $ad_id = '1466881926004';
        }
    }

    $geo = explode(',', $_REQUEST['geo']);
    $net = $_REQUEST['net'] == 2 ? 2 : 6; # 2 WIFIs 6 4G
//    $onlyCare = $_REQUEST['_only_care'];
    $in = '体育';
//    switch($onlyCare) {
//        case 1:
//            $in = '篮球';
//            break;
//        case 2:
//            $in = '足球';
//            break;
//        default:
//            $in = '篮球,足球';
//    }

    $data = array(
        "app" => array(
            "id"=>$ad_id,//1468119553016,
            "bundle"=>$bundle,
        ),
        "imp"=>array(
            "native"=>array(
                "layout"=>0
            ),
            "secure"=>0,
            "trackertype"=>"url_ping",
            "ext"=>array(
                "ads"=>1
            ),
        ),
        "device"=>array(
            //"ifa"=>empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'],
            "ua"=>$_SERVER['HTTP_USER_AGENT'],
            "ip"=>getenv("REMOTE_ADDR"),
            'connectiontype' => $net,
            'geo'=>array(
                'lat' => floatval($geo[1]),
                'lon' => floatval($geo[0]),
                'accu' => 0,
                'type' => 1
            ),
            'ext'=>array(
                'orientation' => 1
            )
        ),
        'user'=>array(
            'ext'=>array(
                'interests' => $in,
                'age'=>mt_rand(20, 35),
            ),
            'gender'=>'m'
        ),
        "ext"=>array(
            "responseformat"=>"json"
        )
    );
    if($platform=='ios') {
        $data['device']['ifa'] = empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'];
    } else {
        $data['device']['o1'] = empty($_REQUEST['android_id'])?'':$_REQUEST['android_id'];
        $data['device']['um5'] = empty($_REQUEST['adid'])?'':md5($_REQUEST['adid']);
        $data['device']['iem'] = empty($_REQUEST['imei'])?'':$_REQUEST['imei'];
    }
    $data_string = json_encode($data);


//    if(!empty($_REQUEST['imei']) && ($_REQUEST['imei']=='355905071724620' || $_REQUEST['imei']=='862298031887877' || $_REQUEST['imei']=='862298031887885')){
//        echo $data_string;
//        exit();
//    }


    $url = 'http://api.w.inmobi.com/showad/v3';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        )
    );
    $res = curl_exec($ch);//echo $res;exit();
    if(!empty($_REQUEST['debug'])){
        echo $res;exit();
    }
    $res_arr = json_decode($res,true);

    if(!empty($res_arr['ads'])){

        addFilledCount($platform,$ad_name,'inmobi',$redis);//统计 填充计数

        $pubContent = base64_decode($res_arr['ads'][0]['pubContent']);

        $pubContentArr = json_decode($pubContent,true);

        $title = $pubContentArr['title'];
        if(mb_strlen($title,'gb2312')<32){
            $title = $pubContentArr['title'].' '.$pubContentArr['description'];
        }

        $title = str_ireplace('【','',$title);
        $title = str_ireplace('】','',$title);
        $title = str_ireplace('《','',$title);
        $title = str_ireplace('》','',$title);
        $title = str_ireplace('（','',$title);
        $title = str_ireplace('）','',$title);

//        if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
//            //$title = mb_strlen($title)>16 ?mb_substr($title,0,16).'..':$title;
//            //$title = Onens::g_length($title)>50?Onens::g_substr($title,50,true):$title;
//            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                $title = Onens::g_length($title)>50?Onens::g_substr($title,50,true):$title;
//            }else{
//                $title = Onens::g_length($title)>30?Onens::g_substr($title,30,true):$title;
//            }
//        }else{
//            //$title = mb_strlen($title)>30 ?mb_substr($title,0,30).'..':$title;
//            $title = Onens::g_length($title)>60?Onens::g_substr($title,60,true):$title;
//        }

        $title = limitContent($title,$ad_name);

        $ad_arr = array(
            'name'=>$ad_name.' inmobi'.$ad_id,
//            'model'=>'direct',
            'type'=>'txt_img',
            'showTimes'=>9999,
            'duration'=>3,
            'img'=>empty($pubContentArr['screenshots']['url'])?$pubContentArr['icon']['url']:$pubContentArr['screenshots']['url'],
            'content'=>$title,//$pubContentArr['description'],//description title
            'url'=>$pubContentArr['landingURL'],
            'position'=>4,
            'act'=>'web',
            'ua_ping_urls'=>$res_arr['ads'][0]['eventTracking']['18']['urls'],
            'down_ping_urls'=>empty($res_arr['ads'][0]['eventTracking']['120']['urls'])?array():$res_arr['ads'][0]['eventTracking']['120']['urls'],
            'show_ping_urls'=>array(),
            'click_ping_urls'=>array(),
            'ua_click_ping_urls'=>$res_arr['ads'][0]['eventTracking']['8']['urls'],
        );

        //$ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_inmobi&t='.time();
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=inmobi&t='.time();
        //$ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'_inmobiClick'.'&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=inmobi&t='.time();


//        if($platform=='android' && stripos($ad_name,'splash')!==false){
//            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=inmobi20170510splash&all=1&t='.time();
//            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=inmobi20170510splashClick&all=1&t='.time();
//        }

        $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'inmobi',$redis);

        if(stripos($ad_name,'neiye')!==false){
//            $ad_arr['type'] = 'banner_img';
//            $ad_arr['ratio'] = '32:5';
        }elseif(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

        collectMaterial($ad_arr,$platform,$ad_name,'inmobi',$redis);

        //屏蔽关键字
        $ad_arr['shop'] = 'inmobi';
        if(Tools::isBadAdvert($ad_arr)){
            collectErrMaterial($ad_arr,$platform,$ad_name,'inmobi',$redis);
            return false;
        }
//        if(stripos($ad_arr['content'],'乐视体育')!==false || stripos($ad_arr['content'],'男人强壮的秘密')!==false || stripos($ad_arr['content'],'屏蔽掉')!==false || stripos($ad_arr['content'],'耐克')!==false || stripos($ad_arr['content'],'阿迪')!==false || stripos($ad_arr['content'],'彩票')!==false || stripos($ad_arr['content'],'竞彩')!==false || stripos($ad_arr['content'],'58同城')!==false){
//            return false;
//        }
//
//        if(stripos($ad_arr['url'],'enyumy.com')!==false || stripos($ad_arr['url'],'fjnjsbw.com')!==false || stripos($ad_arr['url'],'kztydx.com')!==false || stripos($ad_arr['url'],'nike619.com')!==false || stripos($ad_arr['url'],'biddingx.com')!==false || stripos($ad_arr['url'],'fastapi.net')!==false){
//            return false;
//        }

        return $ad_arr;
    }else{
        return false;
    }
}


function getGuangdiantongAdvert($platform,$ad_name){
    $ad_arr = array(
        'id'=>'',
        'name'=>$ad_name,
        'type'=>'图片',//
        'model'=>'sdk_gdt',
        'img'=>'',
        'content'=>'',
        'url'=>$platform=='android'?'3020414804642506':'1040819895589963',
        'showTimes' => 999,
        'duration' => 3,
        'position'=>4,
        'group'=>0,
        'act'=>'web',
        'ua_ping_urls'=>array(
            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&t='.time(),
        ),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(
            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&t='.time()
        ),
        'click_ping_urls'=>array(),
    );

    //安卓
//    3090013943404807 新闻频道
//7050819923306838 重要
//7050419994943129 视频频道


    if(stripos($ad_name,'main_important')!==false){
//    $ad_arr['type'] = 'txt_img';
//    $ad_arr['model'] = 'sdk_gdt';
//    $ad_arr['url'] = $platform=='android'?'1010119876512822':'1030211876313823';
//        $ad_arr['type'] = 'sdk';
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'7050819923306838':'1030211876313823';
    }elseif (stripos($ad_name,'main_news')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'3090013943404807':'1030211876313823';
    }elseif (stripos($ad_name,'news_list')!==false){
//        $ad_arr['type'] = 'banner_img';
//        $ad_arr['model'] = 'sdk_gdt';
//        $ad_arr['ratio'] = '16:9';
//        $ad_arr['url'] = $platform=='android'?'1010119876512822':'1030211876313823';
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'3090013943404807':'1030211876313823';
    }elseif (stripos($ad_name,'video_list')!==false){
//        $ad_arr['type'] = 'banner_img';
//        $ad_arr['model'] = 'direct';
//        $ad_arr['img'] = 'http://iflyad.bj.openstorage.cn/gnometest/duodian/480_320.jpg';
//        $ad_arr['content'] = '广告精准投放,应用快速变现,独家定制语音互动广告';
//        $ad_arr['desc'] = '这里是描述信息ABCDEFG1234567890';
//        $ad_arr['ratio'] = '6:1';
//        $ad_arr['type'] = 'banner_img';
//        $ad_arr['model'] = 'sdk_gdt';
//        $ad_arr['ratio'] = '16:9';
//        $ad_arr['url'] = $platform=='android'?'1010119876512822':'1030211876313823';
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'7050419994943129':'1030211876313823';
    }elseif (stripos($ad_name,'news_neiye')!==false){
//    $ad_arr['type'] = 'banner_img';
//    $ad_arr['model'] = 'direct';
//    $ad_arr['img'] = 'http://iflyad.bj.openstorage.cn/gnometest/duodian/480_320.jpg';
//    $ad_arr['content'] = '广告精准投放,应用快速变现,独家定制语音互动广告';
//    $ad_arr['ratio'] = '8:1';

//        $ad_arr['type'] = 'banner_img';
//        $ad_arr['model'] = 'sdk_gdt';
//        $ad_arr['ratio'] = '16:9';
//        $ad_arr['url'] = $platform=='android'?'1010119876512822':'1030211876313823';

        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'1010119876512822':'1030211876313823';
    }elseif (stripos($ad_name,'relation')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_gdt';
        $ad_arr['url'] = $platform=='android'?'6000412914649280':'4070322169558411';
    }elseif (stripos($ad_name,'main_splash')!==false){

    }


    if(!empty($_REQUEST['_dev'])){
        $ad_arr['position'] = 10;
    }



    $ad_arr['ban'] = array(
        'url'=>array(),//array('jd','mall'),
        'words'=>array('乐视体育','彩票','竞彩'),//array('急售','消消乐','三国','这手游画质太唯美了','电梯监控','游戏'),
    );
    $ad_arr['try_time'] = 5;

    return $ad_arr;
}

function getBaiduAdvert($platform,$ad_name){
    $ad_arr = array(
        'id'=>'',
        'name'=>$ad_name,
        'type'=>'图片',//
        'model'=>'sdk_baidu',
        'img'=>'',
        'content'=>'',
        'url'=>$platform=='android'?'3454133':'3454134',
        'showTimes' => 999,
        'duration' => 3,
        'position'=>4,
        'group'=>0,
        'act'=>'web',
        'ua_ping_urls'=>array(
            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&t='.time(),
        ),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(
            'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&t='.time()
        ),
        'click_ping_urls'=>array(),
    );

    if(stripos($ad_name,'main_splash')===false){
        $ad_arr['type'] = 'txt_img';
        //$ad_arr['type'] = 'sdk';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3481083':'3481071';
    }

    if(stripos($ad_name,'main_important')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3531008':'3531013';
    }elseif (stripos($ad_name,'main_news')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3531008':'3531013';
    }elseif (stripos($ad_name,'news_list')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3531008':'3531013';
    }elseif (stripos($ad_name,'video_list')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3531008':'3531013';
    }elseif (stripos($ad_name,'news_neiye')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['model'] = 'sdk_baidu';
        $ad_arr['url'] = $platform=='android'?'3531008':'3531013';
    }
//elseif (stripos($ad_name,'main_splash')!==false){
//
//    }


    if(!empty($_REQUEST['_dev'])){
        $ad_arr['position'] = 5;
    }



    $ad_arr['ban'] = array(
        'url'=>array(),//array('jd','mall'),
        'words'=>array('商标注册','手游','信用卡','商标注册','移民','装修','体育','乐视体育','彩票','竞彩'),//array('急售','消消乐','三国','这手游画质太唯美了','电梯监控','游戏'),
    );
    $ad_arr['try_time'] = 5;

    return $ad_arr;
}



function getChangsiBannerAdvert($platform,$ad_name,$redis=null) {
    if(isOverByShowCount($platform,$ad_name,'changsi',$redis)){
        return false;
    }
    addRequestCount($platform,$ad_name,'changsi',$redis);

    $api_url = 'http://service.cocounion.com/core/ssp/bid/chance';
    $bn = $_REQUEST['vendor'];
    $mn = $_REQUEST['model'];
//0 Other
//1 Android
//2 IOS
//3 WindowsPhone
    $ost = $platform == 'android'?1:2;
    if($ost == 1) {
        $version = $_REQUEST['version_name'];
    } else {
        $version = $_REQUEST['version_code'];
    }
    $osv = $_REQUEST['osv'];
    $anm = urlencode('直播吧');
    $pnm = $platform=='android'?'android.zhibo8':'com.zhibo8.client81136870';
    $idfa = empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei'])?'':$_REQUEST['imei'];
    $anid = empty($_REQUEST['adid'])?'':$_REQUEST['adid'];
    $mac = empty($_REQUEST['mac'])?'':$_REQUEST['mac'];
    $net = $_REQUEST['net'];
    if($net==2){
        $net = 1;//1;//wifi
    }else{
        $net = 4;//2;//数据网络
    }
    list($t1, $t2) = explode(' ', microtime());
    $time = intval(($t1 + $t2) * 1000);

    $ip = getenv("REMOTE_ADDR");
    if($platform == 'android') {
        $os = 1;
        $pid = '815443115-8E6B89-DDF6-6503-F0D2FFA7D';
        $positionid = '815443115oo6zxm';
        $version = $_REQUEST['version_name'];
        $devicetype = $_REQUEST['devicetype'] == 0 ? 4 : 5; // 0 phone 1 pad  4 android phone 5 android pad
        //$pnm = 'android.zhibo8';
    } else {
        $os = 0;
        $pid = '815442493-755FB6-5064-7F40-946E4FFFA'; #iOS
        $positionid = '815442493oo703b';
        $version = $_REQUEST['version_code'];
        $devicetype = $_REQUEST['devicetype'] == 0 ? 1 : 2; // 0 iphone 1 ipad
        //$pnm = 'com.zhibo8.client81136870';
    }
    $adtype = 1;

    $data = array (
        'id' => md5(uniqid()),
        'imp' =>
            array (
                array (
                    'positionid' => $positionid,
                    'adtype' => $adtype,
                    'w' => '320',
                    'h' => '50',
                    'wmax' => '320',
                    'hmax' => '50',
                    'wmin' => '320',
                    'hmin' => '50',
                    'pos' => '0',
                    'ctype' =>
                        array (
                            '1',
                        ),
                ),
            ),
        'app' =>
            array (
                'pid' => $pid,
                'cat' =>
                    array (
                    ),
                'bundle' => $pnm,
                'name' => $anm,
                'paid' => '0',
                'appv' => $version,
            ),
        'device' =>
            array (
                'type' => $devicetype,
                'ip' => $ip,
                'ua' => $_SERVER['HTTP_USER_AGENT'],
                'geo' =>
                    array (
                        'cc' => 'CN',
                    ),
                'imei' => $imei,
                'imsi' => '',
                'anid' => $anid,
                'mac' => $mac,
                'lang' => 'zh',
                'brand' => $bn,
                'model' => $mn,
                'dn' => '', # 需要的参数先留空
                'os' => $os,
                'osv' => $osv,
                'js' => '1',
                'conntype' => $net,
                'sw' => $_REQUEST['dvw'],
                'sh' => $_REQUEST['dvh'],
                'den' => $_REQUEST['density'],
                'ori' => $_REQUEST['orientation'] == 0 ? 1 : 2,
                'jb' => '0',
                'kst' => '',# 需要的参数先留空
                'rm' => $mac,
                'rs' => isset($_REQUEST['ssid']) ? $_REQUEST['ssid'] : '',
                'ifa' => $idfa
            ),
        'time' => $time,
        'ssl' => '0',
        'ext' => '',
    );
    $s2s = md5("pid={$pid}&adtype={$adtype}&timestamp={$time}&os={$os}&secret=6CA47AAA997DEFCD0F8FDAA62873E0D3");
    $dataField = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataField);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-CAD-S2S-VER: 2.2.3",
        "X-CAD-S2S-SIGNITURE: {$s2s}",
        'Content-Type: application/json; charset=utf-8'
    ]);

    $res = curl_exec($ch);

    $res_arr = json_decode($res,true);

//    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa']=='3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
//        echo 'ook123';exit();
//    }

    if(!empty($res_arr['result']) && $res_arr['result']){
        addFilledCount($platform,$ad_name,'changsi',$redis);//统计 填充计数
        foreach ($res_arr['ads'] as $ad){
            $img = isset($ad['stuffurl']) ? $ad['stuffurl'] : $ad['stuffurls'][0];
            $ad_arr = array(
                'name'=>$ad_name.' changsi',
                'status'=>'enable',
                'type'=>'banner_img',
                'ratio'=>'320:50',
                'showTimes'=>9999,
                'duration'=>3,
                'img'=>$img,
                'content'=>'',
                'url'=>$ad['curl'],
                'module'=>'',
                'position'=>4,
                'act'=>'web',
                'ua_ping_urls'=>$ad['impmonurl'], // 展示上报
                'down_ping_urls'=>array(),
                'show_ping_urls'=>array(),
                'ua_click_ping_urls'=>$ad['clkmonurl'], # click
                'click_ping_urls'=>array(),
            );

            foreach ($ad_arr['ua_ping_urls'] as $k=>$value){
                $ad_arr['ua_ping_urls'][$k] .= '&_t='.time();
            }
            foreach ($ad_arr['click_ping_urls'] as $k=>$value){
                $ad_arr['click_ping_urls'][$k] .= '&_t='.time();
            }

            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=changsi&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=changsi&t='.time();

            $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'changsi',$redis);

            //屏蔽关键字
            $ad_arr['shop'] = 'changsi';
            if(Tools::isBadAdvert($ad_arr)){
                return false;
            }

            return $ad_arr;
        }
        return false;
    }else{
        return false;
    }
}


function getXinshuAdvert($platform,$ad_name,$redis=null) {
    if(isOverByShowCount($platform,$ad_name,'xinshu',$redis)){
        return false;
    }
    addRequestCount($platform,$ad_name,'xinshu',$redis);
//    if($platform=='ios'){
//        $rnd = mt_rand(1,3);
//        if($rnd!=1){
//            return false;
//        }
//    }
    $api_url = 'http://cmarket.kejet.net/adr';
    $model = $_REQUEST['model'];
    $ost = $platform == 'android'?1:2;
    if($ost == 1) {
        $version = $_REQUEST['version_name'];
    } else {
        $version = $_REQUEST['version_code'];
    }
    $osv = $_REQUEST['osv'];
    $anm = urlencode('直播吧');
    $idfa = empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei'])?'':$_REQUEST['imei'];
    $anid = empty($_REQUEST['adid'])?'':$_REQUEST['adid'];
    $mac = empty($_REQUEST['mac'])?'':$_REQUEST['mac'];
    $net = $_REQUEST['net'];
    if($net==2){
        $net = 1;//1;//wifi
    }else{
        $net = 4;//2;//数据网络
    }
    $isMain = stripos($ad_name,'main_splash')!==false;

    $size = $isMain ? '640x960':'400x300';
    if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $size ='300x200';
        return false;
    }

    $ip = getenv("REMOTE_ADDR");
    if($platform == 'android') {
        $pid = $isMain ? 'ICI45XKCT9FYEPMLEQI7' : 'V0SEFEK06RH1TO5MZVUO';
        $appid = 'android.zhibo8';
        if(stripos($ad_name,'news_list')!==false || stripos($ad_name,'main_news')!==false){
            $pid = 'NT5NH1FZPYAVERRUGI9L';
        }elseif(stripos($ad_name,'video_list')!==false){
            $pid = 'T9EWIQGYPI9ZJKPM5WNT';
        }elseif(stripos($ad_name,'main_important')!==false){
            $pid = 'KRDVB6NSTQEJUFS1VJ6W';
        }
    } else {
        $pid = $isMain ? '1ZTURZVR5ZX1FNEACPGP' : 'G4QYIEP90MDB5MBDOKN6'; #iOS
        $appid = 'com.zhibo8.client81136870';

        if(stripos($ad_name,'news_list')!==false || stripos($ad_name,'main_news')!==false){
            $pid = 'RGCIUJRCKAQ5XH7C9Y62';
        }elseif(stripos($ad_name,'video_list')!==false){
            $pid = '2KRWRKGGYU28KSRWWR3E';
        }elseif(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
            $pid = 'MRWYFUDJLYOSAENZTJ7P';
        }
    }
    $arr = [
        'pid' => $pid,
        'apitype' => 'native',
        'platform' => strtoupper($platform),
        'appid' => $appid,
        'appname' => $anm,
        'appver' => $version,
        'isapp' => 'Y',
        'mac' => $mac,
        'imei' => $imei,
        'androidid' => $anid,
        'idfa' => $idfa,

        'size' => $size,
        'adzlocation' => 2,
        'detected_language' => 'zh',
        'rid' => uniqid(),
        'ip' => $ip,
        'ua' => $_SERVER['HTTP_USER_AGENT'],
        'model' => $model,
        'ver' => $osv,
        'width' => $_REQUEST['dvw'],
        'height' => $_REQUEST['dvh'],
        'istabledevice' => 'N',
        'networdtype' => $net == 1 ? 'WIFI':'4G'
    ];
    $apiUrl = $api_url.'?'. http_build_query($arr);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_TIMEOUT,2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
//    if(!empty($_REQUEST['idfa']) && ($_REQUEST['idfa']=='3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27' )){
//        echo $res;
//        exit();
//    }
    $ad = json_decode($res,true);
    if(!empty($ad['adurl']) && $ad['adurl']){
        addFilledCount($platform,$ad_name,'xinshu',$redis);//统计 填充计数
        $ad_arr = array(
            'name'=>$ad_name.' xinshu',
            'status'=>'enable',
            'type'=>'txt_img',
            'showTimes'=>9999,
            'duration'=>3,
            'img'=>$ad['adurl'],
            'content'=>$ad['body'],
            'url'=>$ad['landingpage_url'],
            'module'=>'',
            'position'=>4,
            'act'=>'web',
            'ua_ping_urls'=>$ad['pm'], // 展示上报
            'down_ping_urls'=>array(),
            'show_ping_urls'=>array(),
            'ua_click_ping_urls'=>isset($ad['cm'])?$ad['cm']:[], # click
            'click_ping_urls'=>array(),
        );

//        if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
//            //$ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            }else{
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>30?Onens::g_substr($ad_arr['content'],30,true):$ad_arr['content'];
//            }
//        }else{
//            $ad_arr['content'] = Onens::g_length($ad_arr['content'])>60?Onens::g_substr($ad_arr['content'],60,true):$ad_arr['content'];
//        }

        $ad_arr['content'] = limitContent($ad_arr['content'],$ad_name);

        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

        foreach ($ad_arr['ua_ping_urls'] as $k=>$value){
            $ad_arr['ua_ping_urls'][$k] .= '&_t='.time();
        }
        foreach ($ad_arr['click_ping_urls'] as $k=>$value){
            $ad_arr['click_ping_urls'][$k] .= '&_t='.time();
        }

        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=xinshu&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=xinshu&t='.time();

//        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=xinshu2&all=1&t='.time();
//        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=xinshu2Click&all=1&t='.time();



        if($platform=='ios'){
            if(stripos($ad_name,'news_list')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82804939&cid=234956&mid=222579&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82804939&cid=234956&mid=222579&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }elseif(stripos($ad_name,'video_list')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82814596&cid=234957&mid=222580&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82814596&cid=234957&mid=222580&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }elseif(stripos($ad_name,'main_important')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19068965_82826055&cid=234954&mid=222577&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19068965_82826055&cid=234954&mid=222577&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }
        }else{
            if(stripos($ad_name,'news_list')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82804707&cid=235489&mid=223132&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82804707&cid=235489&mid=223132&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }elseif(stripos($ad_name,'video_list')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82802815&cid=235490&mid=223133&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82802815&cid=235490&mid=223133&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }elseif(stripos($ad_name,'main_important')!==false){
                $ad_arr['ua_ping_urls'][] = 'http://afptrack.alimama.com/imp?pid=mm_119500411_19072852_82802606&cid=235488&mid=223131&oid=32271&productType=1';
                $ad_arr['ua_click_ping_urls'][] = 'http://afptrack.alimama.com/clk?pid=mm_119500411_19072852_82802606&cid=235488&mid=223131&oid=32271&productType=1&t=ECZ96RyplVNkvgrzES%2BMA9BILhrTwmdeKm6oczKP6nI%3D';
            }
        }
//        else{
//            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=xinshuAndroid&all=1&t='.time();
//            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=xinshuAndroidClick&all=1&t='.time();
//        }

        $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'xinshu',$redis);

        //屏蔽关键字
        $ad_arr['shop'] = 'xinshu';
        if(Tools::isBadAdvert($ad_arr)){
            return false;
        }

        return $ad_arr;
    }else{
        return false;
    }
}


function getKuaiyouAdvert($platform,$ad_name,$redis=null)
{
    $dsp_name = 'kuaiyou';
    addRequestCount($platform, $ad_name, $dsp_name, $redis);

    $api_url = 'https://open.adview.cn/agent/openRequest.do';
    $model = $_REQUEST['model'];
    $ost = $platform == 'android' ? 1 : 2;
    if ($ost == 1) {
        $version = $_REQUEST['version_name'];
    } else {
        $version = $_REQUEST['version_code'];
    }
    $osv = $_REQUEST['osv'];
    $anm = urlencode('直播吧');
    $idfa = empty($_REQUEST['idfa']) ? '' : $_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei']) ? '' : $_REQUEST['imei'];
    $anid = empty($_REQUEST['adid']) ? '' : $_REQUEST['adid'];
    $mac = empty($_REQUEST['mac']) ? '' : $_REQUEST['mac'];
    $net = $_REQUEST['net'];
    if ($net == 2) {
        $net = 1;//1;//wifi
    } else {
        $net = 4;//2;//数据网络
    }
    $isMain = stripos($ad_name, 'main_splash') !== false;

    $ip = $_REQUEST['ip'];
    if ($platform == 'android') {
        $osPlatform = 0;
        $pack = 'android.zhibo8';
        $posId = 'POSIDti5x4iuopypc';
        $pid = 'SDK20171517030852ec1b1gzd32irtee';
    } else {
        $osPlatform = 1;
        $pack = 'com.zhibo8.client81136870';
        $posId = 'NATIVEue8dnsn48xuh';
        $pid = 'SDK20171110110857ksiujjv2d6sz75m';
    }

    $sn = $osPlatform == 0 ? $imei : $idfa;
    list($t1, $t2) = explode(' ', microtime());
    $ts = (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    $arr = [
        'n' => 1,
        'appid' => $pid,
        'pt' => 6,
        'posId' => $posId,
        'w' => $_REQUEST['dvw'],
        'h' => $_REQUEST['dvh'],
        'sw' => $_REQUEST['dvw'],
        'sh' => $_REQUEST['dvh'],
        'ip' => '121.40.77.123',
        'os' => $osPlatform,
        'bdr' => $osv,
        'tp' => $model,
        'brd' => $_REQUEST['vendor'],
        'andt' => 0,
        'sn' => $sn,
        'idfa' => $idfa,

        'idfv' => '',

        'mc' => $mac,
        'andid' => $anid,
        'nt' => $net == 1 ? 'WIFI' : '4G',
        'nop' => '',
        'tab' => 0,
        'ua' => $_SERVER['HTTP_USER_AGENT'],
        'tm' => 0,
        'pack' => $pack,
        'time' => $ts,
        'token' => md5($pid . $sn . $osPlatform . '' . $pack . $ts . 'ohd0ap3na2v7is4pea3icwiyhmg5yvk8'), ################
    ];
    if (stripos($ad_name, 'main_splash') !== false) {
        $arr['pt'] = 4;
        #$arr['at'] = 5;
        $arr['sw'] = 640;
        $arr['sh'] = 960;
    }
    $apiUrl = $api_url . '?' . http_build_query($arr);
    $res = file_get_contents($apiUrl);
    $ads = json_decode($res, true);
    if (stripos($ad_name, 'main_splash') !== false) {
        if (!isset($ads['ad'][0]['api'][0])) {
            return false;
        }
    } elseif (!isset($ads['ad'][0]['native'])) {
        return false;
    }
    $ad = $ads['ad'][0];
    if (isset($ads['ad'][0]['api'][0]) || (!empty($ad['native']['images'][0]['url']) && $ad['native']['images'][0]['url'])) {
        addFilledCount($platform, $ad_name, $dsp_name, $redis);//统计 填充计数
        if (stripos($ad_name, 'main_splash') !== false) {
            $ad_arr = array(
                'name' => $ad_name . ' ' . $dsp_name,
                'status' => 'enable',
                'type' => 'main_splash',
                'showTimes' => 9999,
                'duration' => 3,
                'img' => $ad['api'][0],
                'content' => '',
                'url' => $ad['al'],
                'module' => '',
                'position' => 4,
                'act' => 'web',
                'ua_ping_urls' => isset($ad['es'][0]) ? $ad['es'][0] : [], // 展示上报
                'down_ping_urls' => array(),
                'show_ping_urls' => array(),
                'ua_click_ping_urls' => isset($ad['ec']) ? $ad['ec'] : [], # click
                'click_ping_urls' => array(),
            );
        } else {
            $ad_arr = array(
                'name' => $ad_name . ' ' . $dsp_name,
                'status' => 'enable',
                'type' => 'txt_img',
                'showTimes' => 9999,
                'duration' => 3,
                'img' => $ad['native']['images'][0]['url'],
                'content' => $ad['native']['desc'],
                'url' => $ad['al'],
                'module' => '',
                'position' => 4,
                'act' => 'web',
                'ua_ping_urls' => array(),
                'down_ping_urls' => array(),
                'show_ping_urls' => isset($ad['es'][0]) ? $ad['es'][0] : [], // 展示上报,
                'ua_click_ping_urls' => array(),
                'click_ping_urls' => isset($ad['ec']) ? $ad['ec'] : [], # click
            );
        }

//        if (stripos($ad_name, 'main_important') !== false || stripos($ad_name, 'main_attention') !== false) {
//            //$ad_arr['content'] = Onens::g_length($ad_arr['content']) > 50 ? Onens::g_substr($ad_arr['content'], 50, true) : $ad_arr['content'];
//            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            }else{
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>30?Onens::g_substr($ad_arr['content'],30,true):$ad_arr['content'];
//            }
//        } else {
//            $ad_arr['content'] = Onens::g_length($ad_arr['content']) > 60 ? Onens::g_substr($ad_arr['content'], 60, true) : $ad_arr['content'];
//        }
        $ad_arr['content'] = limitContent($ad_arr['content'],$ad_name);

        if (stripos($ad_name, 'main_splash') !== false) {
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

//        foreach ($ad_arr['ua_ping_urls'] as $k => $value) {
//            $ad_arr['ua_ping_urls'][$k] .= '&_t=' . time();
//        }
//        foreach ($ad_arr['click_ping_urls'] as $k => $value) {
//            $ad_arr['click_ping_urls'][$k] .= '&_t=' . time();
//        }

        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=' . $ad_name . '&chl=' . $dsp_name . '&t=' . time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position=' . $ad_name . 'Click' . '&chl=' . $dsp_name . '&t=' . time();

        $ad_arr = addPingUrl($ad_arr, $platform, $ad_name, $dsp_name, $redis);

        collectMaterial($ad_arr,$platform,$ad_name,'kuaiyou',$redis);

        //屏蔽关键字
        $ad_arr['shop'] = 'kuaiyou';
        if (Tools::isBadAdvert($ad_arr)) {
            collectErrMaterial($ad_arr,$platform,$ad_name,'kuaiyou',$redis);
            return false;
        }

        return $ad_arr;
    } else {
        return false;
    }
}

function getJushaAdvert($platform,$ad_name, $redis=null) {
    if($platform != 'ios'){
        return false;
    }

    $dsp_name = 'jusha';
    addRequestCount($platform,$ad_name,$dsp_name,$redis);

    // API 基础数据
    $API_URL = 'https://appapisdk.tanv.com/zhiboba/FullScreen.php';//'https://appapisdk.tanv.com/1_0_0/FullScreen.php';//'http://appapisdk.jusha.com/zbb/FullScreen.php';//'https://appapisdk.jusha.com/1_1_0/FullScreen.php';//
    $APP_ID = "38";
    $APP_KEY = "3f9c1fa1e1c97c795b3e2d895b699289";//"625e86509c7e304429585d60d80a4b6e";
    $USER_ID = "44";
    $APP_BUNDLE_ID = "com.zhibo8.client81136870";

    $idfa = empty($_REQUEST['idfa']) ? '' : $_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei']) ? '' : $_REQUEST['imei'];
    $anid = empty($_REQUEST['adid']) ? '' : $_REQUEST['adid'];
    $mac = empty($_REQUEST['mac']) ? '' : $_REQUEST['mac'];
    $vonderID = $_REQUEST['vendor'];

    // 操作系统
    $os = $_REQUEST["os"] == "iOS" ? "IOS" : "Android";
    if ($os == "Android")
    {
        $idfa = $imei;
    }

    // 屏幕高宽
    $heightPixels = $_REQUEST["dvh"];
    $widthPixels = $_REQUEST["dvw"];

    // 网络状态
    $net = $_REQUEST['net'] == 2 ? $_REQUEST['net'] : 1; # 1 WIFIs 4 GPRS

    $checkKey = md5($USER_ID . $APP_ID . $APP_KEY);

    // 是否竖屏, 这里默认都是竖屏, 1竖屏，2横屏
    $isS = 1;

    // clientinfo
    $clientInfo = [
        "IDFA" => $idfa,                           # 广告唯一ID，（android的要IMEI值）
        "VendorID" => $vonderID,                    # 软件开发商唯一ID
        "NetState" => $net,                         # 1:GPRS   2:WiFi
        "DeviceName" => "",                         # 用户自己设置的手机名称
        "SystemName" => "",                         # 系统名称
        "SystemVersion" => "",                      # 系统版本
        "AppVerion" => $os == 'Android' ? $_REQUEST['version_name'] : $_REQUEST['version_code'],                          # app版本
        "BatteryLevel" => "",                       # 剩余电量
        "AppName" => 'zhibo8',                       # 当前应用名称
        "BundleIdentifier" => "",                   # BundleID（后台获取的）
        "LocalizedModel" => "",                     # 型号
        "DeviceModel" => "",                        # 设备型号
        "CarrierName" => "",                        # 运营商名称
        "MagneticHeading" => "",                    # 方向
        "WifiName" => "",                           # wifi名称
        "GravityX" => "",                           # 重力感应X轴
        "GravityY" => "",                           # 重力感应Y轴
        "GravityZ" => "",                           # 重力感应Z轴
        "TotalDiskSpace" => "",                     # 总容量
        "UsedDiskSpace" => "",                      # 剩余容量
    ];

    $postData = [
        "UserID" => $USER_ID,
        "AppID" => $APP_ID,
        "CheckKey" => $checkKey,
        "WidthPixels" => $widthPixels,
        "HeightPixels" => $heightPixels,
        "System" => $os,
        "NetState" => $net,
        "BundleIdentifier" => $APP_BUNDLE_ID,
        "ClientInfo" => json_encode($clientInfo),
        "is_s" => $isS,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $res = curl_exec($ch);

    $ad = json_decode($res, true);

    if ($ad["Status"] != 1001)
    {
        return false;
    }

    if(!empty($ad["Data"]['AdsUrl']) && $ad["Data"]['AdsUrl']){
        addFilledCount($platform, $ad_name,$dsp_name, $redis);//统计 填充计数
        $ad_arr = array(
            'name' => $ad_name.' AdsUrl',
            'status' => 'enable',
            'type' => 'txt_img',
            'showTimes' => 9999,
            'duration' => 3,
            'img' => $ad["Data"]['AdsImgUrl'],
            'content' => $ad['Info'],
            'url' => $ad["Data"]['AdsUrl'],
            'module' => '',
            'position' => 4,
            'act' => 'web',
            'ua_ping_urls' => array(),
            'down_ping_urls' => array(),
            'show_ping_urls' => [$ad["Data"]["PvStatisticsUrl"]], // 展示上报
            'ua_click_ping_urls' => [], # click
            'click_ping_urls' => [$ad["Data"]["ClickStatisticsUrl"]],
        );

//        if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
//            //$ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            }else{
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>30?Onens::g_substr($ad_arr['content'],30,true):$ad_arr['content'];
//            }
//        }else{
//            $ad_arr['content'] = Onens::g_length($ad_arr['content'])>60?Onens::g_substr($ad_arr['content'],60,true):$ad_arr['content'];
//        }
        $ad_arr['content'] = limitContent($ad_arr['content'],$ad_name);

        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl='.$dsp_name.'&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl='.$dsp_name.'&t='.time();


        $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,$dsp_name,$redis);

        collectMaterial($ad_arr,$platform,$ad_name,'jusha',$redis);

        //屏蔽关键字
        $ad_arr['shop'] = 'jusha';
        if(Tools::isBadAdvert($ad_arr)){
            collectErrMaterial($ad_arr,$platform,$ad_name,'jusha',$redis);
            return false;
        }

        return $ad_arr;
    }else{
        return false;
    }
}


function getMoyichengAdvert($platform,$ad_name, $redis=null) {
//    if(stripos($ad_name,'list')===false){
//        return false;
//    }

    $dsp_name = 'moyicheng';
    addRequestCount($platform, $ad_name, $dsp_name, $redis);

    // API 基础数据
    $API_URL = 'http://adpssp.ad-mex.com/adRequest';

    $APP_BUNDLE_ID = $_REQUEST["os"] == "iOS" ? "com.zhibo8.client81136870" : 'android.zhibo8';
    $os = $_REQUEST["os"];
    if($_REQUEST["os"] == "iOS") {
        $APP_BUNDLE_ID = 'com.zhibo8.client81136870';
        $appid = '59a3d46f9297140100b489e9';
        if(strpos($ad_name, 'list') !== false){
            $adposid = '59a3d75e9297140100b48a57';
        } elseif(strpos($ad_name, 'neiye') !== false) {
            $adposid = '59a3d738c5a6e401002d028c';
        }
    } else {
        $APP_BUNDLE_ID = 'android.zhibo8';
        $appid = '59a3d4f7c5a6e401002d0234';
        if(strpos($ad_name, 'list') !== false){
            $adposid = '59a3d704c5a6e401002d027c';
        } elseif(strpos($ad_name, 'neiye') !== false) {
            $adposid = '59a3d6af9297140100b48a31';
        }
    }

    if(!isset($adposid)) {
        return false;
    }

    $idfa = empty($_REQUEST['idfa']) ? '' : $_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei']) ? '' : $_REQUEST['imei'];
    $anid = empty($_REQUEST['adid']) ? '' : $_REQUEST['adid'];
    $mac = empty($_REQUEST['mac']) ? '' : $_REQUEST['mac'];
    $vonderID = $_REQUEST['vendor'];

    // 屏幕高宽
    $heightPixels = $_REQUEST["dvh"];
    $widthPixels = $_REQUEST["dvw"];

    // 网络状态
    $net = $_REQUEST['net'] == 2 ? $_REQUEST['net'] : 1; # 1 WIFIs 4 GPRS

    // 网络状态
    $net = $_REQUEST['net'] == 2 ? 'wifi' : '4G'; # 1 WIFIs 4 GPRS

    $geo = explode(',', $_REQUEST['geo']);
    $postData = [
        'apiver' => '2.0',
        'publisherid' => '59a3c5e8c5a6e401002cffcc',
        'appid' => $appid,
        'adposid' => $adposid,
        'token' => '6305d9b39a3cd5b1a03d398e655c0d47',
        'bundleid' => $APP_BUNDLE_ID,
        'device' => [
            'ua' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'devicetype' => 'phone',
            'make' => $_REQUEST['vendor'],
            'model' => $_REQUEST['model'],
            'os' => $os,
            'osv' => $_REQUEST['osv'],
            'mac' => $mac,
            'connectiontype' => $net,
            'hwh' => intval($heightPixels),
            'hww' => intval($widthPixels),
            'geo' => [
                'lat' => floatval($geo[1]),
                'lon' => floatval($geo[0]),
                'timestamp' => time()
            ]
        ],
        'imp' => [
            'js' => 0,
            'native' => [
                'required' => ['3'],
                'type' => 2,
                'img' => [
                    [
                        'h' => 300,
                        'w' => 400
                    ]
                ],
            ]
        ],
    ];
    if ($os == "Android") {
        $postData['device']['imei'] = $imei;
        $postData['device']['androidid'] = $anid;
    } else {
        $postData['device']['idfa'] = $idfa;
    }
    $data_string = json_encode($postData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );
    $res = curl_exec($ch);
    $http = curl_getinfo($ch);
    $ad = json_decode($res, true);
    if (!isset($ad['adposid']) || $ad['adposid'] != $postData['adposid']){
        return false;
    }

    if(!empty($ad['adm']) && $ad['adm']){
        addFilledCount($platform, $ad_name, $dsp_name, $redis);
        $adm = json_decode($ad['adm'], true);
        $ad_arr = array(
            'name' => $ad_name.' Mobi',
            'status' => 'enable',
            'type' => 'txt_img',
            'showTimes' => 9999,
            'duration' => 3,
            'img' => $adm['imgurl'][0][0],
            'content' => $adm['title']['text'],
            'url' => $adm['curl'],
            'module' => '',
            'position' => 4,
            'act' => 'web',
            'ua_ping_urls' => $ad['imp_url'], // 展示上报
            'down_ping_urls' => array(),
            'show_ping_urls' => array(),
            'ua_click_ping_urls' => [], # click
            'click_ping_urls' => $ad['click_url'],
        );

//        if(stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
//            //$ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>50?Onens::g_substr($ad_arr['content'],50,true):$ad_arr['content'];
//            }else{
//                $ad_arr['content'] = Onens::g_length($ad_arr['content'])>30?Onens::g_substr($ad_arr['content'],30,true):$ad_arr['content'];
//            }
//        }else{
//            $ad_arr['content'] = Onens::g_length($ad_arr['content'])>60?Onens::g_substr($ad_arr['content'],60,true):$ad_arr['content'];
//        }
        $ad_arr['content'] = limitContent($ad_arr['content'],$ad_name);

        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

        foreach ($ad_arr['ua_ping_urls'] as $k=>$value){
            $ad_arr['ua_ping_urls'][$k] .= '&_t='.time();
        }
        foreach ($ad_arr['click_ping_urls'] as $k=>$value){
            $ad_arr['click_ping_urls'][$k] .= '&_t='.time();
        }

        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl='.$dsp_name.'&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl='.$dsp_name.'&t='.time();


        $ad_arr = addPingUrl($ad_arr, $platform, $ad_name, $dsp_name, $redis);

        collectMaterial($ad_arr,$platform,$ad_name,$dsp_name,$redis);

        //屏蔽关键字
        $ad_arr['shop'] = $dsp_name;
        if (Tools::isBadAdvert($ad_arr)) {
            collectErrMaterial($ad_arr,$platform,$ad_name,$dsp_name,$redis);
            return false;
        }

        return $ad_arr;
    }else{
        return false;
    }
}

function getShanghaiAdvert($platform,$ad_name,$redis){
//    if($platform!='android' && stripos($ad_name,'room')===false){//只处理安卓
//        return false;
//    }
    $ad_key = '';
    $ad_img = '';
    //if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
    if($platform=='android'){
        if(stripos($ad_name,'main_result')!==false){
            $ad_key = 'caipiao_main_result';
            $ad_img = 'http://ggtu.qiumibao.com/ad/zongbu/20170818_wansai_600_100.png';
            $title = '';
            $url = 'http://cai.zuqiuxing.com/api/download/turn';
            $ad_type = 'banner_img';
            $position = 99;//放最后
            $ua_ping_url = '';
            $ua_click_ping_url = '';
        }
        if(stripos($ad_name,'main_important')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-20 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620' && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_important';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';
            $title = '嘉士伯携手利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'banner_img';
            $position = 99;//放最后
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896770,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896770,c1817,i0,m101,8a2,8b2,h';
        }

        if(stripos($ad_name,'main_attention')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-17 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620' && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_attention';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
            $title = '嘉士伯与利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'txt_img';
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896771,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896771,c1817,i0,m101,8a2,8b2,h';
        }

        if(stripos($ad_name,'main_news')!==false && ( empty($_REQUEST['label']) || (!empty($_REQUEST['label']) && $_REQUEST['label']=='全部'))){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-17 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_news_all';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
            $title = '嘉士伯与利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'txt_img';
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896772,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896772,c1817,i0,m101,8a2,8b2,h';
        }



        if(stripos($ad_name,'main_news')!==false && !empty($_REQUEST['label']) && ($_REQUEST['label']=='中超' || $_REQUEST['label']=='西甲' || $_REQUEST['label']=='英超' || $_REQUEST['label']=='德甲' || $_REQUEST['label']=='意甲' || $_REQUEST['label']=='法甲' || $_REQUEST['label']=='精华' || $_REQUEST['label']=='足彩' )){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2018-05-14 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_news_child';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_child_banner_600x100.jpg';//
            $title = '';
            $url = '';
            $ad_type = 'banner_img';
            $ua_ping_url = '';
            $ua_click_ping_url = '';
            $position = 0;
        }

        if(stripos($ad_name,'news_list')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-17 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_news_list';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
            $title = '嘉士伯与利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'txt_img';
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896772,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896772,c1817,i0,m101,8a2,8b2,h';
        }

        if(stripos($ad_name,'video_list')!==false){
            if(!empty($_REQUEST['label']) && ($_REQUEST['label']=='中超' || $_REQUEST['label']=='西甲' || $_REQUEST['label']=='英超' || $_REQUEST['label']=='德甲' || $_REQUEST['label']=='意甲' || $_REQUEST['label']=='法甲' || $_REQUEST['label']=='精华' || $_REQUEST['label']=='足彩' )){

            }else{
                return false;
            }
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2018-05-14 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_video_list_child';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_child_banner_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/0831_600_100.jpg';//
            $title = '';
            $url = '';
            $ad_type = 'banner_img';
            $ua_ping_url = '';
            $ua_click_ping_url = '';
            $position = 0;
        }
    }elseif($platform=='ios'){
        $version_ios = empty($_REQUEST['version_code'])?'':$_REQUEST['version_code'];
        $version_ios_val = (int)str_ireplace('.','',$version_ios);

        if(stripos($ad_name,'main_result')!==false && $version_ios_val>450){
            $ad_key = 'caipiao_main_result';
            $ad_img = 'http://ggtu.qiumibao.com/ad/zongbu/20170818_wansai_600_100.png';
            $title = '';
            $url = 'http://cai.zuqiuxing.com/api/download/turn';
            $ad_type = 'banner_img';
            $position = 99;//放最后
            $ua_ping_url = '';
            $ua_click_ping_url = '';
        }

        if(stripos($ad_name,'main_important')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-20 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }else{
                return false;
            }

            if($version_ios_val>=459){
                $ad_key = 'jiashibo_main_important_ios';
                $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';
                $title = '嘉士伯携手利物浦25周年纪念罐';
                $url = 'http://item.jd.com/4682899.html';
                $ad_type = 'banner_img';
                $position = 99;//放最后
                $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896770,c1817,i0,m202,8a2,8b2,h';
                $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896770,c1817,i0,m101,8a2,8b2,h';
            }else{
                $ad_key = 'jiashibo_main_important_ios';
                $ad_img = 'http://ggtu.qiumibao.com/0812jsbgz.jpg';
                $title = '嘉士伯与利物浦25周年纪念罐';
                $url = 'http://item.jd.com/4682899.html';
                $ad_type = 'txt_img';
                $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896770,c1817,i0,m202,8a2,8b2,h';
                $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896770,c1817,i0,m101,8a2,8b2,h';
            }
        }



        if(stripos($ad_name,'main_news')!==false && ( empty($_REQUEST['label']) || (!empty($_REQUEST['label']) && $_REQUEST['label']=='全部'))){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-17 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_news_all_ios';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
            $title = '嘉士伯与利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'txt_img';
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896772,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896772,c1817,i0,m101,8a2,8b2,h';
        }



        if(stripos($ad_name,'main_news')!==false && !empty($_REQUEST['label']) && ($_REQUEST['label']=='中超' || $_REQUEST['label']=='西甲' || $_REQUEST['label']=='英超' || $_REQUEST['label']=='德甲' || $_REQUEST['label']=='意甲' || $_REQUEST['label']=='法甲' || $_REQUEST['label']=='精华' || $_REQUEST['label']=='足彩' )){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2018-05-14 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_main_news_child_ios';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_child_banner_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/0831_600_100.jpg';//
            $title = '';
            $url = '';
            $ad_type = 'banner_img';
            $ua_ping_url = '';
            $ua_click_ping_url = '';
            $position = 0;
        }

        if(stripos($ad_name,'news_list')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2017-08-17 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            $ad_key = 'jiashibo_news_list_ios';
            $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
            $title = '嘉士伯与利物浦25周年纪念罐';
            $url = 'http://item.jd.com/4682899.html';
            $ad_type = 'txt_img';
            $ua_ping_url = 'http://v.admaster.com.cn/i/a90786,b1896772,c1817,i0,m202,8a2,8b2,h';
            $ua_click_ping_url = 'http://clickc.admaster.com.cn/c/a90786,b1896772,c1817,i0,m101,8a2,8b2,h';
        }

        if(stripos($ad_name,'video_list')!==false){
            $start_time_str = '2017-08-12 00:00';
            $end_time_str = '2018-05-14 00:00';
            if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

            }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'  && false){

            }else{
                return false;
            }

            if(!empty($_REQUEST['label']) && ($_REQUEST['label']=='中超' || $_REQUEST['label']=='西甲' || $_REQUEST['label']=='英超' || $_REQUEST['label']=='德甲' || $_REQUEST['label']=='意甲' || $_REQUEST['label']=='法甲' || $_REQUEST['label']=='精华' || $_REQUEST['label']=='足彩' )){
                $ad_key = 'jiashibo_video_list_child_ios';
                $ad_img = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_child_banner_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/0831_600_100.jpg';//
                $title = '';
                $url = '';
                $ad_type = 'banner_img';
                $ua_ping_url = '';
                $ua_click_ping_url = '';
                $position = 0;
            }
        }

    }


    //version_code	4.5.9
    $version_ios = empty($_REQUEST['version_code'])?'':$_REQUEST['version_code'];
    $version_ios_val = (int)str_ireplace('.','',$version_ios);
    if(stripos($ad_name,'room')!==false && ($platform=='android' || ( $platform=='ios' &&  !empty($_REQUEST['version_code']) && $version_ios_val>=458) )){
        $ad_key = 'room_'.$ad_name;
        $ad_img = '';//'http://ggtu.qiumibao.com/ad/zongbu/20170814_lottery_banner_1.png';
        $title = '新人送108元（金山彩票）';
        $url = $platform=='android'?'http://cai.zuqiuxing.com/api/download/turn':'http://cai.zuqiuxing.com/api/download/turn';
        $ad_type = 'txt_img';
        $ua_ping_url = '';
        $ua_click_ping_url = '';
        $position = 4;
    }

    //if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
    if(stripos($ad_name,'main_news')!==false && false){
        $start_time_str = '2017-08-05 22:00';
        $duration = time() - strtotime($start_time_str);
        if($duration>0 && $duration<3600*24){
            $ad_key = 'guangzhou20170804';//$duration<3600*24?'meidi3':'meidi4';
            $ad_count = $redis->get('ad_'.md5($ad_key));
            if($ad_count>1000000){
                return false;
            }
            $ad_img = 'http://ggtu.qiumibao.com/ad/guangzhou/0805_300_200.jpg';
            $title = '等球赛开场？不如先来组团烧猪！';
            $url = 'http://uri6.com/n6niie';
            $ad_type = 'txt_img';
            $ua_ping_url = '';
            $ua_click_ping_url = '';
        }
    }

    if(!empty($ad_img) && !empty($ad_key)){
        $ad_arr = array(
            'id'=>1,
            'name'=>'上海',
            'status'=>'enable',
            'platform'=>'',
            'placement'=>'',
            'type'=>$ad_type,//'txt_img','banner_img','图片'
            'showTimes'=>9999,
            'duration'=>3,
            'img'=>$ad_img,
            'monopolize'=>'disable',
            'module'=>'',
            'content'=>$title,
            'url'=>$url,
            'position'=>4,
            'act'=>'web',
            'ua_ping_urls'=>array(),
            'down_ping_urls'=>array(),
            'show_ping_urls'=>array(),
            'ua_click_ping_urls'=>array(),
            'click_ping_urls'=>array(),
        );

        if($ad_arr['type'] == 'banner_img'){
            $ad_arr['ratio'] = '6:1';
            if(isset($position)){
                $ad_arr['position'] =  $position;
            }
        }

        if(!empty($ad_name)){
            if(!empty($ua_ping_url)){
                $ad_arr['show_ping_urls'][] = $ua_ping_url;
            }
            if(!empty($ua_click_ping_url)){
                $ad_arr['click_ping_urls'][] = $ua_click_ping_url;
            }
            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=shanghai&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=shanghai&t='.time();
            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'&all=1&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'Click&all=1&t='.time();
        }

        return $ad_arr;
    }
    return false;
}

function getFillAdvert($platform,$ad_name,$redis=null){
    $ad_arr = false;
    if(true){

        $start_time_str = '2017-10-23 12:25';
        $end_time_str = '2017-11-12 00:00';
        if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)){

        }else{
            return false;
        }
    //
        $ad_key = '20171019shuang11';
    //        $count_key = 'ad_'.date("Y-m-d").'_'.md5($ad_key);
    //        $count = $redis->get($count_key);
    //        if($count>1000000){
    //            return false;
    //        }
        $ad_arr = array(
            'id'=>1,
            'name'=>'双11'.$ad_name,
            'status'=>'enable',
            'platform'=>'',
            'placement'=>'',
            'type'=>'txt_img',
            'showTimes'=>9999,
            'duration'=>3,
            'img'=>'http://ggtu.qiumibao.com/ad/zongbu/20171025shuang11_400_300.jpg',
            'monopolize'=>'disable',
            'module'=>'',
            'content'=>'天猫双11超级红包，最高1111元等你来抢！',
            'url'=>'https://s.click.taobao.com/jYVBPZw',
            'position'=>9,
            'ratio'=>'5:1',
            'act'=>'web',
            'ua_ping_urls'=>array(),
            'down_ping_urls'=>array(),
            'show_ping_urls'=>array(),
            'ua_click_ping_urls'=>array(),
            'click_ping_urls'=>array(),
        );



        if(!empty($ad_name)){
            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=fill_advert&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=fill_advert&t='.time();
            $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'&all=1&t='.time();
            $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'Click&all=1&t='.time();
        }
    }
    return $ad_arr;
}

function getSignalAdvert($platform,$ad_name,$redis=null){
    $ad_arr = array(
        'id'=>1,
        'name'=>'信号列表',
        'status'=>'enable',
        'platform'=>'',
        'placement'=>'',
        'type'=>'txt_img',
        'showTimes'=>9999,
        'duration'=>3,
        'img'=>'',
        'monopolize'=>'disable',
        'module'=>'',
        'content'=>'新人送108元（金山彩票）',
        'url'=>'http://cai.zuqiuxing.com/api/download/turn',
        'position'=>99,
        'ratio'=>'5:1',
        'act'=>'web',
        'ua_ping_urls'=>array(),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(),
        'click_ping_urls'=>array(),
    );



    if(!empty($ad_name)){
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=shanghai&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=shanghai&t='.time();
    }

    return $ad_arr;
}

function getZhiboNav($platform,$ad_name,$redis=null){
    $start_time_str = '2017-08-12 00:00';
    $end_time_str = '2018-05-14 00:00';
    if(time()>strtotime($start_time_str) && time()<strtotime($end_time_str)) {

    }elseif(!empty($_REQUEST['imei']) && $_REQUEST['imei'] == '355905071724620'){

    }elseif(!empty($_REQUEST['_debug']) && $_REQUEST['_debug']==1){

    }else{
        return false;
    }

    if(!empty($_REQUEST['label']) && (stripos($_REQUEST['label'],'足球') !== false && stripos($_REQUEST['label'],'直播内页') !== false )){

    }else{
        return false;
    }

    //    if($platform == 'android'){
    //        return false;
    //    }
    $ad_arr = array(
        'id'=>1,
        'name'=>'直播间',
        'status'=>'enable',
        'platform'=>'',
        'placement'=>'',
        'type'=>'txt_img',
        'showTimes'=>9999,
        'duration'=>3,
        'img'=>'',
        'monopolize'=>'disable',
        'module'=>'',
        'content'=>'嘉士伯 Carlsberg',
        'url'=>'',
        'position'=>0,
        'ratio'=>'5:1',
        'act'=>'web',
        'ua_ping_urls'=>array(),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(),
        'click_ping_urls'=>array(),
    );

    if($platform=='android' || $platform=='ios'){
       $ad_arr['img'] = 'http://ggtu.qiumibao.com/ad/shanghai/20170825_320_90_1.png';//'http://ggtu.qiumibao.com/ad/shanghai/20170822_guanming_linshi_320_50.PNG';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_logo_250x50_2.png';
        $ad_arr['content'] = '';
        $ad_arr['ratio'] = '32:9';
    }

    if(!empty($_REQUEST['_debug']) && $_REQUEST['_debug']==1){
        $ad_arr['img'] = 'http://ggtu.qiumibao.com/ad/shanghai/20170825_320_90_2.png';//'http://ggtu.qiumibao.com/ad/shanghai/1_120.png';
        $ad_arr['content'] = '';
        $ad_arr['ratio'] = '320:90';
    }

    if(!empty($_REQUEST['nav_info']) && (stripos($_REQUEST['nav_info'],'群侃足坛战术') !== false )){
        $ad_arr['img'] = '';
        $ad_arr['content'] = '乱世王者特约专题';
    }


    if(!empty($ad_name)){
        $ad_key = 'ad_key_test';
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=shanghai&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=shanghai&t='.time();
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'&all=1&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'Click&all=1&t='.time();
    }
    //return false;
    return $ad_arr;
}

function getTestAdvert($platform,$ad_name,$redis=null){
    if(stripos($ad_name,'news_list')!==false){
        return false;
    }
    $ad_arr = array(
        'id'=>1,
        'name'=>'测试',
        'status'=>'enable',
        'platform'=>'',
        'placement'=>'',
        'type'=>'banner_img',//banner_img txt_img
        'showTimes'=>9999,
        'duration'=>3,
        'img'=>'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg',
        'monopolize'=>'disable',
        'module'=>'',
        'content'=>'嘉士伯携手利物浦25周年纪念罐',
        'url'=>'http://item.jd.com/4682899.html',
        'position'=>4,
        'ratio'=>'6:1',
        'act'=>'web',
        'ua_ping_urls'=>array(),
        'down_ping_urls'=>array(),
        'show_ping_urls'=>array(),
        'ua_click_ping_urls'=>array(),
        'click_ping_urls'=>array(),
    );

    if(stripos($ad_name,'signal_list')!==false){
        $ad_arr['type'] = 'banner_img';
        $ad_arr['ratio'] = $platform=='android'?'996:59':'749:54';
        $ad_arr['img'] = $platform=='android'?'http://ggtu.qiumibao.com/ad/zongbu/20170815_signal_android.png':'http://ggtu.qiumibao.com/ad/zongbu/20170815_signal_ios.png';
        $ad_arr['content'] = '';
        $ad_arr['url'] = 'https://at.umeng.com/mu4vWr';
        $ad_arr['position'] = 99;
    }

    if(stripos($ad_name,'main_important')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['img'] = 'http://ggtu.qiumibao.com/0830jsbzy.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
        $ad_arr['content'] = '嘉士伯与利物浦25周年纪念罐';//'詹俊眼中的利物浦';
        $ad_arr['url'] = 'https://v.qq.com/x/page/l0543je7ms2.html';
    }elseif(stripos($ad_name,'main_news')!==false || stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false){
        $ad_arr['type'] = 'txt_img';
        $ad_arr['img'] = 'http://ggtu.qiumibao.com/ad/shanghai/20170811_jiashibo_300_200.jpg';
        $ad_arr['content'] = '嘉士伯与利物浦25周年纪念罐';
        if( (stripos($ad_name,'main_news')!==false || stripos($ad_name,'video_list')!==false) && !empty($_REQUEST['label']) && ($_REQUEST['label']=='中超' || $_REQUEST['label']=='西甲' || $_REQUEST['label']=='英超' || $_REQUEST['label']=='德甲' || $_REQUEST['label']=='意甲' || $_REQUEST['label']=='法甲' || $_REQUEST['label']=='精华' || $_REQUEST['label']=='足彩' )){
            $ad_arr['img'] = 'http://ggtu.qiumibao.com/ad/shanghai/20171030_banner_main_600x100.jpg';//'http://ggtu.qiumibao.com/ad/shanghai/20170811_child_banner_600x100.jpg';//'http://ggtu.qiumibao.com/0830jsbbanner2.jpg';//
            $ad_arr['type'] = 'banner_img';
            $ad_arr['position'] = 0;
            $ad_arr['content'] = '';
            $ad_arr['url'] = '';
        }
    }elseif (stripos($ad_name,'splash')!==false){
        $ad_arr['type'] = '图片';
        $ad_arr['placement'] = '开机画面';
        $ad_arr['module'] = '';
        $ad_arr['img'] = $platform=='android'?'http://ggtu.qiumibao.com/0830jsbaz4.jpg':'http://ggtu.qiumibao.com/0830jsbios.jpg';
        $ad_arr['url'] = 'https://v.qq.com/x/page/l0543je7ms2.html';
    }

    if(!empty($ad_name)){
        $ad_key = 'ad_key_test';
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=shanghai&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=shanghai&t='.time();
        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'&all=1&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_key.'Click&all=1&t='.time();
    }
    //return false;
    return $ad_arr;
}

function getDuomengAdvert($platform,$ad_name,$redis=null) {
    //    if(isOverByShowCount($platform,$ad_name,'duomeng',$redis)){
    //        return false;
    //    }
    addRequestCount($platform,$ad_name,'duomeng',$redis);

    $api_url = 'http://p.ts.domob.cn/';
    $model = $_REQUEST['model'];
    $ost = $platform == 'android'?1:2;
    if($ost == 1) {
        $version = $_REQUEST['version_name'];
    } else {
        $version = empty($_REQUEST['version_code'])?'':$_REQUEST['version_code'];
    }
    $osv = $_REQUEST['osv'];
    $anm = urlencode('直播吧');
    $idfa = empty($_REQUEST['idfa'])?'':$_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei'])?'':$_REQUEST['imei'];
    $anid = empty($_REQUEST['adid'])?'':$_REQUEST['adid'];
    $mac = empty($_REQUEST['mac'])?'':$_REQUEST['mac'];
    $net = $_REQUEST['net'];
    if($net==2){
        $net = 1;//1;//wifi
    }else{
        $net = 4;//2;//数据网络
    }

    $ip = getenv("REMOTE_ADDR");
    if($platform == 'android') {
        $appid = 'android.zhibo8';
    } else {
        $appid = 'com.zhibo8.client81136870';
    }
    $arr = [
        '_a' => $ost == 1 ? 1891454130:1444911136,
        '_t' => 24,
        '_os' => $ost,
        '_pgn' => $appid,
        '_appname' => $anm,
        '_appversion' => $version,
        '_ip' => $ip,
        '_ua' => $_SERVER['HTTP_USER_AGENT'],
        '_md' => $model,
        '_osv' => $osv,
        '_w' => 640,
        '_h' => 960,
        '_adw' => 640,
        '_adh' => 960,
        '_nt' => $net == 1 ? 1:4
    ];
    isset($_REQUEST['operator']) && $_REQUEST['operator'] && $arr['_o'] = $_REQUEST['operator'];
    isset($_REQUEST['vendor']) && $_REQUEST['vendor'] && $arr['_dev'] = $_REQUEST['vendor'];
    if($arr['_os'] == 1) {
        $arr['_aid'] = $anid;
        $arr['_imeio'] = $imei;
        $arr['_mc'] = $mac;
    } else {
        $arr['_idfa'] = $idfa;
    }
    if(!$arr['_appversion']) {
        unset($arr['_appversion']);
    }
    $apiUrl = $api_url.'?'. http_build_query($arr);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_TIMEOUT,2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    //    if(!empty($_REQUEST['idfa']) && ($_REQUEST['idfa']=='3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27' )){
    //        echo $res;
    //        exit();
    //    }
    $ad = json_decode($res,true);
    if(!empty($ad['adid']) && $ad['adid']){
        addFilledCount($platform,$ad_name,'duomeng',$redis);//统计 填充计数
        $ad_arr = array(
            'name'=>$ad_name.' duomeng',
            'status'=>'enable',
            'type'=>'txt_img',
            'showTimes'=>9999,
            'duration'=>3,
            'img'=>$ad['img'][0],
            'content'=>'',
            'url'=>$ad['lp'],
            'module'=>'',
            'position'=>4,
            'act'=>'web',
            'ua_ping_urls'=>$ad['pm'], // 展示上报
            'down_ping_urls'=>array(),
            'show_ping_urls'=>array(),
            'ua_click_ping_urls'=>$ad['cm'], # click
            'click_ping_urls'=>array(),
        );

        if(stripos($ad_name,'main_splash')!==false){
            $ad_arr['type'] = '图片';
            $ad_arr['placement'] = '开机画面';
            $ad_arr['module'] = '';
        }

        foreach ($ad_arr['ua_ping_urls'] as $k=>$value){
            $ad_arr['ua_ping_urls'][$k] .= '&_t='.time();
        }

        $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl=duomeng&t='.time();
        $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl=duomeng&t='.time();


        $ad_arr = addPingUrl($ad_arr,$platform,$ad_name,'duomeng',$redis);

        //屏蔽关键字
        $ad_arr['shop'] = 'duomeng';
        if(Tools::isBadAdvert($ad_arr)){
            return false;
        }

        return $ad_arr;
    }else{
        return false;
    }
}

function getJingDongAdvert($platform, $ad_name, $redis=null) {
    $dsp_name = 'jingdongjinrong';
    //请求数统计
    addRequestCount($platform, $ad_name, $dsp_name, $redis);

    $debug = false;

    //请求处理
    include './Com/Jd/Jr/Adx/Proto/BidRequest.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_App.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_App_Device.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_Imp.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_Imp_Native.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_Imp_Video.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_Imp_ViewScreen.php';
    include './Com/Jd/Jr/Adx/Proto/BidRequest_User.php';
    //返回处理
    include './Com/Jd/Jr/Adx/Proto/BidResponse.php';
    include './Com/Jd/Jr/Adx/Proto/BidResponse_Ads.php';
    include './Com/Jd/Jr/Adx/Proto/BidResponse_Ads_Attr.php';
    include './Com/Jd/Jr/Adx/Proto/BidResponse_Ads_Video.php';

    //定价
    $API_URL_COMMON = 'http://adx.jd.com/ssp/common';
    //竞价
    $API_URL_BIDDING = 'http://adx.jd.com/ssp/bidding';
    //使用定价接口
    $API_URL = $API_URL_COMMON;

    //初始化参数（注意数据类型必须和文档要求一致，有些可选参数，可不设置）
    // integer: required 当前协议版本号, 目前版本号为 1
    $version    = 1;
    // string: required 唯一的请求 ID，32 字节的字符串
    $request_id = md5(uniqid());
    // bool 是否测试请求[default = false]
    $test       = false;
    // string 系统分配给 SSP 用户的 token
    $ssp_token  = "deb9000253955875";
    // string 用户的 IP 地址
    $ip         = getenv('REMOTE_ADDR');
    // string 用户的浏览器类型即HTTP请求头部的User-Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    // string 用户的默认系统语言
    $language   = 'zh';

    // Object: repeated Imp 对象的数组,最少需要 imp 对象
     //imp数据()
    $imp = [];
    // string 系统分配给应用的唯一标识
    $imp['media_id']        = !empty($_REQUEST['media_id']) ? $_REQUEST['media_id'] : '8141000008845858';
    // string 系统分配给广告位的唯一标识
    $imp['ad_id']       = !empty($_REQUEST['ad_id']) ? $_REQUEST['ad_id'] : 'c6ac000008845860';
    // string 推广位的尺寸: 宽 x 高
    if (stripos($ad_name,'main_splash')!==false) {
        $imp['size'] = '720x1280';
    }else{
        $imp['size'] = '400x300';
    }
    // integer 最低竞标价格，货币单位为人民币，数值含义为分/千次展现
    $imp['floor_price']     = !empty($_REQUEST['floor_price']) ? $_REQUEST['floor_price'] : 500;
    // integer 广告位展现形式 参加字典 dict-view-type.txt
    //202开屏 206信息流
    if (stripos($ad_name,'main_splash')!==false) {
        $imp['view_type'] = 202;
    }else{
        //除去开屏的都是信息流页的广告
        $imp['view_type'] = 206;
    }
    //native数据
    $native = [];
    // NativeField:repeated 属性集合 1:标题; 2:广告语; 3:描述; 4:主题图;
    // 5:ICON; 6:click_url; 7:download_url; 8:deep_link;
    // $native['fields']        = [1,2,3,4,5,6];(必须项)

    // Object 移动设备信息
    //app数据
    $app = [];
    // string Application name
    $app['app_name']            = !empty($_REQUEST['app_name']) ? $_REQUEST['app_name'] : '直播吧' ;
    // string 应用包名（例如， com.foo.mygame) 安卓: package name, 苹果: bundleID
    $os = !empty($_REQUEST["os"])? $_REQUEST["os"] : '';
    if ($os == 'iOS') {
        $app['app_package_name'] = 'com.zhibo8.client81136870';
    }else{
        $app['app_package_name'] = 'android.zhibo8';
    }
    //设备数据
    $device = [];
    //string 操作系统（e.g., "android","ios"）
    if ($os == 'iOS') {
        $device['os'] = 'ios';
    }else{
        $device['os'] = 'android';
    }
    //string 操作系统版本（e.g., "7.0.2"）
    $device['osv']          = !empty($_REQUEST['osv']) ? $_REQUEST['osv'] :'7.0.2';
    //string 设备平台（e.g., "iPhone","android","ipad"）
    $device['device_type']  = !empty($_REQUEST['device_type']) ? $_REQUEST['device_type'] :'android';
    //integer 用户设备的屏幕方向 1：竖屏；2：横屏；3：未知；
    $device['orientation']  = !empty($_REQUEST['orientation']) ? $_REQUEST['orientation'] : 1;
    $device['orientation'] = intval($device['orientation']);
    // string 设备品牌（e.g., "Apple"）
    $device['brand']        = !empty($_REQUEST['vendor']) ? $_REQUEST['vendor'] :'HUAWEI';
    // string 设备硬件版本（e.g., "5S"）
    $device['hwv']          = !empty($_REQUEST['model']) ? $_REQUEST['model'] :'P10';
    // integer 设备的网络运营商 0-未知, 1-移动, 2-联通, 3-电信
    $device['operator']     = !empty($_REQUEST['operator']) ? $_REQUEST['operator'] : 0;
    $device['operator'] = intval($device['operator']);
    // integer 设备所处网络环境 0-未识别, 1-wifi, 2-2g, 3-3g,4-4g
    $net = !empty($_REQUEST['net']) ? $_REQUEST['net'] : 4;
    if($net == 2){
        $net = 1;//wifi
    }
    $device['network']      = $net;
    $device['network'] = intval($device['network']);
    $device['ip']      = getenv('REMOTE_ADDR');
    // integer 屏幕高度， 以像素为单位
    $device['H']            = !empty($_REQUEST["dvh"]) ? $_REQUEST["dvh"] : '';
    $device['H'] = intval($device['H']);
    // integer 屏幕宽度，以像素为单位
    $device['W']            = !empty($_REQUEST["dvw"]) ? $_REQUEST["dvw"] : '';
    $device['W'] = intval($device['W']);
    // string 设备的屏幕分辨率（e.g., "1024x768"）
    $device['device_size']  = !empty($_REQUEST['device_size']) ? $_REQUEST['device_size'] :'1024x768';
    // string: required 用户设备唯一标识 对于 IOS 设备，该值为 idfa 对于 android 设备，该值为 imei
    $idfa = empty($_REQUEST['idfa']) ? '' : $_REQUEST['idfa'];
    $imei = empty($_REQUEST['imei']) ? '' : $_REQUEST['imei'];
    if ($os == 'iOS') {
        $device['device_id'] = $idfa;
    }else{
        $device['device_id'] = $imei;
    }
    // string 户设备的 mac 地址
    $device['mac']          = !empty($_REQUEST['mac']) ? $_REQUEST['mac'] :'';
    // string 对于 android 设备, 设置 androidId(IOS 不填)
    $device['android_id']   = !empty($_REQUEST['adid']) ? $_REQUEST['adid'] : '';
    //用户数据
    $user = [];
    // string 年龄 少年/青年/中年/老年
    $user['age']        = !empty($_REQUEST['age']) ? $_REQUEST['age'] : '青年';
    // integer 性别，0 表示男性， 1 表示女性，不填充表示未知
    $user['gender']     = !empty($_REQUEST['gender']) ? $_REQUEST['gender'] : '0';
    $user['gender']     = intval($user['gender']);


    //请求构造
    $req = new Com\Jd\Jr\Adx\Proto\BidRequest();
    $req->setVersion($version);
    $req->setRequestId($request_id);
    $req->setTest($test);
    $req->setSspToken($ssp_token);
    $req->setIp($ip);
    $req->setUserAgent($user_agent);
    $req->setLanguage($language);

    //imp 节点
    $imp_node = new Com\Jd\Jr\Adx\Proto\BidRequest_Imp();
    $imp_node->setMediaId($imp['media_id']);
    $imp_node->setAdId($imp['ad_id']);
    $imp_node->setSize($imp['size']);
    $imp_node->setViewType($imp['view_type']);

    //imp - native广告节点
    $native_node = new Com\Jd\Jr\Adx\Proto\BidRequest_Imp_Native();
    $native_node->appendFields(1);
    $native_node->appendFields(2);
    $native_node->appendFields(3);
    $native_node->appendFields(4);
    $native_node->appendFields(5);
    $native_node->appendFields(6);
    //native 节点结束
    $imp_node->setNative($native_node);
    //imp节点结束
    $req->appendImp($imp_node);

    //APP 节点
    $app_node = new Com\Jd\Jr\Adx\Proto\BidRequest_App();
    $app_node->setAppName($app['app_name']);
    $app_node->setPackageName($app['app_package_name']);

    //APP - device节点(根据不同手机，有些参数不用设置)
    $device_node = new Com\Jd\Jr\Adx\Proto\BidRequest_App_Device();
    $device_node->setOs($device['os']);
    $device_node->setOsv($device['osv']);
    $device_node->setDeviceType($device['device_type']);
    $device_node->setOrientation($device['orientation']);
    $device_node->setBrand($device['brand']);
    $device_node->setHwv($device['hwv']);
    $device_node->setOperator($device['operator']);
    $device_node->setNetwork($device['network']);
    $device_node->setIp($device['ip']);
    $device_node->setH($device['H']);
    $device_node->setW($device['W']);
    $device_node->setDeviceSize($device['device_size']);
    $device_node->setDeviceId($device['device_id']);
    $device_node->setMac($device['mac']);
    if ($device['os'] == 'android') {
        $device_node->setAndroidId($device['android_id']);
    }
    //App-设备节点结束
    $app_node->setDevice($device_node);
    //APP节点结束
    $req->setApp($app_node);

    //user 节点
    $user_node = new Com\Jd\Jr\Adx\Proto\BidRequest_User();
    $user_node->setAge($user['age']);
    $user_node->setGender($user['gender']);
    //user 节点结束
    $req->setUser($user_node);
    //提交
    $to_send_protobuf = $req->serializeToString();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $to_send_protobuf);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/octet-stream',
    ));
    $res = curl_exec($ch);

    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
    //目前的状态码只有200（正常返回）和204(没有匹配到广告)
    if ($httpCode == 204) {
        //没有相关的广告素材
        if ($debug == true) {
            echo "没有相关的广告素材";
        }
        return false;
    }

    if (curl_errno($ch)) {
            if ($debug) {
                echo 'curl error: '. curl_error($ch);
            }
            return false;
    }else{
        $resp = new Com\Jd\Jr\Adx\Proto\BidResponse();
        try{
            $resp->parseFromString($res);
        } catch (Exception $e){
            if ($debug) {
                die('Parse error: '. $e->getMessage());
            }
            return false;
        }
    }

    curl_close($ch);
    $ad_arr = $resp->getAds();
    $ad_content = $ad_arr[0];

    if(!empty($ad_content->getShowUrl())){
        //统计 填充计数
        addFilledCount($platform, $ad_name, $dsp_name, $redis);
    }else{
        return false;
    }


    $return_ad_arr = [
        'name' => $ad_name.' jdjr',
        'status' => 'enable',
        'type' => 'txt_img',
        'showTimes' => 9999,
        'duration' => 3,
        'img' => $ad_content->getImgurl(),
        'content' => '',
        'url' => $ad_content->getLdp(),
        'module' => '',
        'position' => 4,
        'act' => 'web',
        'ua_ping_urls' => [], 
        'down_ping_urls' => array(),
        'show_ping_urls' => $ad_content->getShowUrl(),// 展示上报
        'ua_click_ping_urls' => [], # click
        'click_ping_urls' => $ad_content->getClickUrl(),//广告商自己合并到落地页，不用我们反馈点击。
    ];

    if(stripos($ad_name,'main_splash')!==false){
        $return_ad_arr['type'] = '图片';
        $return_ad_arr['placement'] = '开机画面';
        $return_ad_arr['module'] = '';
    }else{
        //信息流获取标题和图片地址
        $ad_attr = $ad_content->getAttr();
        foreach ($ad_attr as $ad_a){
            if($ad_a->getName()=='title'){
                $return_ad_arr['content'] = $ad_a->getValue();
                continue;
            }
            if($ad_a->getName()=='imgs1'){
                $return_ad_arr['img'] = $ad_a->getValue();
                continue;
            }
            if($ad_a->getName()=='clickUrl'){
                $return_ad_arr['url'] = $ad_a->getValue();
                continue;
            }
        }
    }

    //添加统计地址
    $return_ad_arr['show_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl='.$dsp_name.'&t='.time();
    $return_ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl='.$dsp_name.'&t='.time();

    //收集素材
    $return_ad_arr = addPingUrl($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);
    collectMaterial($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);

    $redis->zAdd('jd_m_all',time(),json_encode($return_ad_arr));

    //屏蔽关键字
    $return_ad_arr['shop'] = $dsp_name;
    if(Tools::isBadAdvert($return_ad_arr)){
        collectErrMaterial($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);
        $redis->zAdd('jd_m_err',time(),json_encode($return_ad_arr));
        return false;
    }

    if ($debug) {
        echo json_encode($return_ad_arr);
    }
    return $return_ad_arr;
}

//今日头条
function getJinRiTouTiaoAdvert($platform, $ad_name, $redis=null) {

    //初始化
    $is_debug       = false;
    //开屏
    $is_main_splash = false;
    $is_iOS         = false;
    $is_Android     = false;
    //信息流
    $is_stream_main_important = false;
    $is_stream_main_news = false;
    $is_stream_news_list = false;
    $is_stream_video_list = false;

    //请求可以使用的参数(2017.10.13安卓穷举)
    // string 用户的 IP 地址
    $ip         = getenv('REMOTE_ADDR');
    // string 用户的浏览器类型即HTTP请求头部的User-Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    //手机系统版本
    $osv        = empty($_REQUEST['osv']) ? '' : $_REQUEST['osv'];
    //wifi ssid
    $ssid       = empty($_REQUEST['ssid']) ? '' : $_REQUEST['ssid'];
    //是否是APP启动
    $is_boot        = empty($_REQUEST['is_boot']) ? '' : $_REQUEST['is_boot'];
    //手机定位 (经,纬)
    $geo        = empty($_REQUEST['geo']) ? '' : $_REQUEST['geo'];
    //应用包名
    $package_name = empty($_REQUEST['package_name']) ? 'android.zhibo8' : $_REQUEST['package_name'];
    //应用包版本号
    $version_name = empty($_REQUEST['version_name']) ? 'v1' : $_REQUEST['version_name'];
    //请求时间戳
    $ts         = empty($_REQUEST['ts']) ? '' : $_REQUEST['ts'];
    //imei
    $imei       = empty($_REQUEST['imei']) ? '' : $_REQUEST['imei'];
    //苹果
    $idfa       = empty($_REQUEST['idfa']) ? '' : $_REQUEST['idfa'];
    //ID 
    $adid       = empty($_REQUEST['adid']) ? '' : $_REQUEST['adid'];
    //屏幕方向(参数未知。。。)
    $orientation = empty($_REQUEST['orientation']) ? '' : $_REQUEST['orientation'];
    //安卓ID
    $android_id = empty($_REQUEST['android_id']) ? '' : $_REQUEST['android_id'];
    //APP名称
    $app_name   = empty($_REQUEST['app_name']) ? '直播吧' : $_REQUEST['app_name'];
    //手机mac
    $mac        = empty($_REQUEST['mac']) ? '' : $_REQUEST['mac'];
    //设备宽
    $dvw        = empty($_REQUEST['dvw']) ? '1920' : $_REQUEST['dvw'];
    //设备高
    $dvh        = empty($_REQUEST['dvh']) ? '1080' : $_REQUEST['dvh'];
    //网络 2表示WiFi 1表示手机蜂窝网络
    $net        = empty($_REQUEST['net']) ? '' : $_REQUEST['net'];
    //手机平台(android或者apple)
    $platform   = empty($_REQUEST['platform']) ? '' : $_REQUEST['platform'];
    //运营商（参数未知。。。）
    $operator   = empty($_REQUEST['operator']) ? '' : $_REQUEST['operator'];
    //手机品牌
    $vendor     = empty($_REQUEST['vendor']) ? '' : $_REQUEST['vendor'];
    //手机型号
    $model      = empty($_REQUEST['model']) ? '' : $_REQUEST['model'];

    //是否开屏,不是开屏的都是信息流
    if (stripos($ad_name, 'main_splash') !== false) {
        $is_main_splash = true;
    }else{
        //判断是哪个页面的信息流
        if (stripos($ad_name, 'main_important') !== false) {
            $is_stream_main_important = true;
        }elseif (stripos($ad_name, 'main_news') !== false) {
            $is_stream_main_news = true;
        }elseif (stripos($ad_name, 'news_list') !== false) {
            $is_stream_news_list = true;
        }elseif (stripos($ad_name, 'video_list') !== false) {
            $is_stream_video_list = true;
        }else{
            //默认为重要信息流
            $is_stream_main_important = true;
        }
    }
    //不是iOS就是Android
    $os = !empty($_REQUEST["os"])? $_REQUEST["os"] : '';
    if ($os == 'iOS') {
        $is_iOS = true;
    }else{
        $is_Android = true;
    }
    

    /*测试 在allOne中可以删除*/
    if (!empty($_REQUEST['is_debug']) && $_REQUEST['is_debug'] == 1) {
        error_reporting(E_ALL);
        // error_reporting(0);
        header('Content-Type:text/html;charset=utf8');
        $is_debug = true;
        if (!empty($_REQUEST['is_main']) && $_REQUEST['is_main'] == 1) {
            $ad_name .= '_main_splash';
            $is_main_splash = true;
        }
    }
    /*测试结束*/


    /**
     * 广告正式开始(注意参数的数据类型)
     */
    $dsp_name   = 'jinritoutiao';
    if ($is_debug == false) {
        //请求数统计
        addRequestCount($platform, $ad_name, $dsp_name, $redis);
    }
    //请求地址
    $API_URL = 'http://is.snssdk.com/api/ad/union/get_ads_json/';

    //构造提交参数
    $app_appid = '';
    $adslots_id = '';
    //appid(媒体id只和手机平台有关) 和 package_name
    if ($is_iOS) {
        $app_appid  = '5000991';
        $package_name = 'com.zhibo8.client81136870';
    }else{
        $app_appid  = '5000990';
        $package_name = 'android.zhibo8';
    }
    //adslots_id
    if ($is_main_splash) {
        if ($is_iOS) {
            $adslots_id = "800991340";
        }else{
            $adslots_id = "800990578";
        }
    }else{
        //信息流分开统计
        if ($is_iOS) {
            //默认为重要信息流
            $adslots_id = "900991322";
            //细分为iOS的哪个信息流
            if ($is_stream_main_important) {
                $adslots_id = "900991322";
            }elseif ($is_stream_main_news) {
                $adslots_id = "900991519";
            }elseif ($is_stream_news_list) {
                $adslots_id = "900991816";
            }elseif ($is_stream_video_list) {
                $adslots_id = "900991753";
            }
        }else{
            //默认为重要信息流
            $adslots_id = "900990100";
            //细分为Android的哪个信息流
            if ($is_stream_main_important) {
                $adslots_id = "900990100";
            }elseif ($is_stream_main_news) {
                $adslots_id = "900990062";
            }elseif ($is_stream_news_list) {
                $adslots_id = "900990763";
            }elseif ($is_stream_video_list) {
                $adslots_id = "900990363";
            }
        }
    }
    $to_post_array['request_id']    = md5(uniqid());
    $to_post_array['api_version']   = '1.6';
    $to_post_array['uid']           = '';
    // //用户信息
    // $to_post_array['user']['age']    = 23;
    $to_post_array['source_type']   = 'app';
    //APP信息
    $to_post_array['app']['appid']  = $app_appid;
    $to_post_array['app']['name']   = $app_name;
    $to_post_array['app']['package_name'] = $package_name;
    //设备信息
    if ($is_iOS) {
        $to_post_array['device']['did'] = $idfa;
    }
    $to_post_array['device']['imei']    = $imei;
    if ($is_Android) {
        $to_post_array['device']['android_id']  = $android_id;
    }
    $to_post_array['device']['type']    = 1;
    if ($is_Android) {
        $to_post_array['device']['os']  = 1;
    }elseif ($is_iOS) {
        $to_post_array['device']['os']  = 2;
    }else{
        $to_post_array['device']['os']  = 0;
    }
    $to_post_array['device']['os_version'] = $osv;
    $to_post_array['device']['vendor']  = $vendor;
    $to_post_array['device']['model']   = $model;
    $to_post_array['device']['language'] = '';
    if ($net == 2) {
        $to_post_array['device']['conn_type'] = 1;
    }else{
        $to_post_array['device']['conn_type'] = 0;
    }
    $to_post_array['device']['mac']     = $mac;
    $to_post_array['device']['screen_width'] = intval($dvw) ;
    $to_post_array['device']['screen_height'] = intval($dvh);
    
    $to_post_array['ua']            = $user_agent;
    $to_post_array['ip']            = $ip;

    //广告信息
    $to_post_array['adslots'][0]['id']          = $adslots_id;
    $to_post_array['adslots'][0]['ad_count']    = 1;
    if ($is_main_splash) {
        $to_post_array['adslots'][0]['adtype']  = 3;
        $to_post_array['adslots'][0]['pos']     = 5;
        $to_post_array['adslots'][0]['accepted_size'][0]['width'] = 720;
        $to_post_array['adslots'][0]['accepted_size'][0]['height'] = 1280;
        // $to_post_array['adslots'][0]['accepted_creative_types'][0] = 2;
        //先不限制交互类型 4为下载，不支持上报(沟通过了，先不用上报 20171016)
        // $to_post_array['adslots'][0]['accepted_interaction_type'][0] = 2;
    }else{
        $to_post_array['adslots'][0]['adtype']  = 5;
        $to_post_array['adslots'][0]['pos']     = 3;
        $to_post_array['adslots'][0]['accepted_size'][0]['width'] = 300;
        $to_post_array['adslots'][0]['accepted_size'][0]['height'] = 400;
        // $to_post_array['adslots'][0]['accepted_creative_types'][0] = 6;
        //先不限制交互类型 4为下载，不支持上报(沟通过了，先不用上报 20171016)
        // $to_post_array['adslots'][0]['accepted_interaction_type'][0] = 2;
    }

    $to_post_json_str = json_encode($to_post_array);
    if (!$to_post_json_str) {
        if ($is_debug) {
            echo 'request error json_encode error';
        }
        return false;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $to_post_json_str);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    //显示头部
    // curl_setopt($ch, CURLOPT_HEADER, true);
    //允许跳转次数
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    $res = curl_exec($ch);

    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 
    if ($httpCode != 200) {
        if ($is_debug == true) {
            echo "response error httpCode: " . $httpCode;
        }
        return false;
    }
    if (curl_errno($ch)) {
        if ($is_debug) {
            echo 'curl error: '. curl_error($ch);
        }
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    //广告内容
    $resArr = json_decode($res, true);
    if (!$resArr) {
        if ($is_debug) {
            echo 'response error json_decode error';
        }
        return false;
    }elseif ($resArr['status_code'] != 20000) {
        if ($is_debug) {
            echo 'response error ERROR_CODE : ' . $resArr['status_code'];
        }
        return false;
    }

    // 根据返回的广告类型填充以下字段
    //设置默认值
    $ad_title = '';
    $ad_explose_click_url = [];
    $ad_explose_show_url = [];
    $ad_img_url = '';
    $ad_ldy_url = '';
    
    if ( !empty($resArr['ads'][0]) ) {
        $ad_content     = $resArr['ads'][0];
        if (empty($ad_content['creative']['creative_type']) || 
            empty($ad_content['creative']['interaction_type']) || 
            empty($ad_content['creative']['click_url']) || 
            empty($ad_content['creative']['show_url']) ||
            empty($ad_content['creative']['image']['url'])
        ) {
            if ($is_debug) {
                echo 'response error ads content parse error 2';
            }
            return false;
        }
        $creative_type      = $ad_content['creative']['creative_type'];
        $interaction_type   = $ad_content['creative']['interaction_type'];
        //填充
        if ($is_main_splash == false) {
            $ad_title               = $ad_content['creative']['title'];
        }
        $ad_explose_click_url   = $ad_content['creative']['click_url'];;
        $ad_explose_show_url    = $ad_content['creative']['show_url'];;
        $ad_img_url             = $ad_content['creative']['image']['url'];
        if ($interaction_type == 4) {
            $ad_ldy_url = $ad_content['creative']['download_url'];
        }else{
            $ad_ldy_url = $ad_content['creative']['target_url'];
        }

    }else{
        if ($is_debug) {
            echo 'response error ads content parse error 1';
        }
        return false;
    }
    
    //组织返回给APP端显示
    $return_ad_arr = [
        'name'      => $ad_name.' '.$dsp_name,
        'status'    => 'enable',
        'type'      => 'txt_img',
        'showTimes' => 9999,
        'duration'  => 3,
        'img'       => $ad_img_url, //显示用的图片地址
        'content'   => '',          //广告的标题
        'url'       => $ad_ldy_url, //广告最终落地地址
        'module'    => '',
        'position'  => 4,
        'act'       => 'web',
        'ua_ping_urls' => [], 
        'down_ping_urls' => array(),
        'show_ping_urls' => $ad_explose_show_url,  //广告 展示 上报地址
        'ua_click_ping_urls' => [], # click
        'click_ping_urls' => $ad_explose_click_url, //广告 点击 上报地址
    ];

    if($is_main_splash){
        $return_ad_arr['type']      = '图片';
        $return_ad_arr['placement'] = '开机画面';
        $return_ad_arr['module']    = '';
    }else{
        //信息流获取标题
        $return_ad_arr['content']   = $ad_title;
    }

    //统计 展示
    $return_ad_arr['show_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'&chl='.$dsp_name.'&t='.time();
    //统计 点击
    $return_ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$ad_name.'Click'.'&chl='.$dsp_name.'&t='.time();

    if ($is_debug == false) {
        //统计 填充计数
        addFilledCount($platform, $ad_name, $dsp_name, $redis);
        //收集素材
        $return_ad_arr = addPingUrl($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);
        collectMaterial($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);
        //屏蔽关键字
        $return_ad_arr['shop'] = $dsp_name;
        if(Tools::isBadAdvert($return_ad_arr)){
            collectErrMaterial($return_ad_arr, $platform, $ad_name, $dsp_name, $redis);
            return false;
        }
    }

    if ($is_debug) {
        $return_ad_arr2[] = $return_ad_arr;
        echo json_encode($return_ad_arr2);
        return $return_ad_arr2;
    }
    return $return_ad_arr;
}

//递归输出广告
function advert_echo($ad_shop_arr,$chk_ad_arr = array(),$platform,$ad_name,$redis=null){
    $advert = advert_rand($ad_shop_arr,$chk_ad_arr);
    if(!empty($advert)){
        $ad_arr = false;
        if($advert['name']=='xunfei'){
            $ad_arr = getXunfeiAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='maiguan'){
            $ad_arr = getMaiguanAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='inmobi'){
            $ad_arr = getInmobiIOSAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='xinshu'){
            $ad_arr = getXinshuAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='kuaiyou'){
            $ad_arr = getKuaiyouAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='jusha'){
            $ad_arr = getJushaAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='toutiao'){
            $ad_arr = getJinRiTouTiaoAdvert($platform,$ad_name,$redis);
        }elseif ($advert['name']=='jdjr'){
            $ad_arr = getJingDongAdvert($platform,$ad_name,$redis);
        }

        if($ad_arr===false && !empty($advert['name'])){
            $chk_ad_arr[] = $advert['name'];
            return advert_echo($ad_shop_arr,$chk_ad_arr,$platform,$ad_name,$redis);
        }else{
            if(!empty($ad_arr)){
                $ad_arr['info'] = 'advert_rand_'.$advert['name'];
            }
            return $ad_arr;
        }
    }
    return false;
}

//根据权重随机取出广告
function advert_rand($ad_shop_arr,$chk_ad_arr){
    $all_ad_arr = array();
    foreach ($ad_shop_arr as $ad){
        if(in_array($ad['name'],$chk_ad_arr)){
            continue;
        }
        for ($i=0;$i<$ad['weight'];$i++){
            $all_ad_arr[] = $ad;
        }
    }

    return empty($all_ad_arr) ? false : $all_ad_arr[array_rand($all_ad_arr,1)];
}


function isOverByShowCount($platform,$ad_name,$ad_shop,$redis){
    $ad_position = '';
    if(stripos($ad_name,'neiye')!==false){
        $ad_position = 'neiye';
    }elseif(stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $ad_position = 'list';
    }elseif(stripos($ad_name,'splash')!==false){
        $ad_position = 'splash';
    }
    $key = $ad_shop.'_'.$ad_position.'_'.$platform.'_request';
    $ad_count = $redis->get('ad_'.date("Y-m-d").'_'.md5($key));

    $over_count = 0;
    if($ad_shop=='xunfei'){
        if($platform=='android'){
            if($ad_position == 'splash'){
                $over_count = 1500000;
            }elseif ($ad_position == 'list'){
                $over_count = 700000;
            }
        }else{
            if($ad_position == 'splash'){
                $over_count = 1000000;
            }elseif ($ad_position == 'list'){
                $over_count = 1000000;
            }
        }
    }elseif($ad_shop=='maiguan'){
        if($platform=='android'){
            if($ad_position == 'splash'){
                $over_count = 3000000;
            }elseif ($ad_position == 'list'){
                $over_count = 1600000;
            }elseif ($ad_position == 'neiye'){
                $over_count = 1000000;
            }
        }else{
            if($ad_position == 'splash'){
                $over_count = 600000;
            }elseif ($ad_position == 'list'){
                $over_count = 3000000;
            }elseif ($ad_position == 'neiye'){
                $over_count = 1000000;
            }
        }
    }elseif($ad_shop=='inmobi'){
        if($platform=='android'){
            if($ad_position == 'splash'){
                $over_count = 1000000;
            }elseif ($ad_position == 'list'){
                $over_count = 1000000;
            }
        }else{
            if($ad_position == 'splash'){
                $over_count = 1000000;
            }elseif ($ad_position == 'list'){
                $over_count = 4000000;
            }elseif ($ad_position == 'neiye'){
                $over_count = 1000000;
            }
        }
    }elseif($ad_shop=='xinshu'){
        if($platform=='android'){
            if ($ad_position == 'list'){
                $over_count = 300000;
            }
        }else{
            if ($ad_position == 'list'){
                $over_count = 1000000;
            }
        }
    }
    //    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
    //        if(($ad_count-$over_count)>0){
    //            echo 'over show:'.$key;
    //        }
    //    }
    return ($ad_count-$over_count)>0;
}

function addPingUrl($ad_arr,$platform,$ad_name,$ad_shop,$redis=null){
    $ad_position = '';
    if(stripos($ad_name,'neiye')!==false){
        $ad_position = 'neiye';
    }elseif(stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $ad_position = 'list';
    }elseif(stripos($ad_name,'splash')!==false){
        $ad_position = 'splash';
    }
    $key = $ad_shop.'_'.$ad_position.'_'.$platform;
//    $count_key = 'ad_'.date("Y-m-d").'_'.md5($key);
//    $count = $redis->incr($count_key);
//    $ad_arr['count'] = $count;
    $ad_arr['ua_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$key.'&t='.time();
    $ad_arr['ua_click_ping_urls'][] = 'http://ggck.qiumibao.com/redirect/count.php?position='.$key.'Click&t='.time();
    return $ad_arr;
}

function addRequestCount($platform,$ad_name,$ad_shop,$redis=null){
    $ad_position = '';
    if(stripos($ad_name,'neiye')!==false){
        $ad_position = 'neiye';
    }elseif(stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $ad_position = 'list';
    }elseif(stripos($ad_name,'splash')!==false){
        $ad_position = 'splash';
    }
    $key = $ad_shop.'_'.$ad_position.'_'.$platform.'_request';
    $count_key = 'ad_'.date("Y-m-d").'_'.md5($key);
    $count = $redis->incr($count_key);

//    $show_count_key =  $ad_shop.'_'.$ad_position.'_'.$platform;
//    if(!empty($_REQUEST['idfa']) && $_REQUEST['idfa'] == '3BF9B6B0-0FBA-41DB-A020-4FA76BC4BA27'){
//        echo $show_count_key.':'.$redis->get($show_count_key).'=====';
//    }
//    $redis->set($count_key,$redis->get($show_count_key));
}

function addFilledCount($platform,$ad_name,$ad_shop,$redis=null){
    $ad_position = '';
    if(stripos($ad_name,'neiye')!==false){
        $ad_position = 'neiye';
    }elseif(stripos($ad_name,'news_list')!==false || stripos($ad_name,'video_list')!==false || stripos($ad_name,'main_news')!==false || stripos($ad_name,'main_important')!==false || stripos($ad_name,'main_attention')!==false){
        $ad_position = 'list';
    }elseif(stripos($ad_name,'splash')!==false){
        $ad_position = 'splash';
    }
    $key = $ad_shop.'_'.$ad_position.'_'.$platform.'_filled';
    $count_key = 'ad_'.date("Y-m-d").'_'.md5($key);
    $count = $redis->incr($count_key);
}

function collectMaterial($ad_arr,$platform,$ad_name,$ad_shop,$redis=null){
    $type = stripos($ad_name,'splash')!==false?'splash':'list';
    $material_arr = array(
        'type'=>$type,
        'img'=>$ad_arr['img'],
        'txt'=>$ad_arr['content'],
        //'url'=>$ad_arr['url'],
        'ad_shop'=>$ad_shop,
    );
    $material_json = json_encode($material_arr);

    if(stripos($ad_arr['img'],'jd.com')===false && stripos($ad_arr['url'],'suning.com')===false && stripos($ad_arr['img'],'360buyimg.com')===false && stripos($ad_arr['img'],'meituan.net')===false && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50026/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50064/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50153/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50289/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50333/')!==false ) ){
        $score = $redis->zScore('material',$material_json);
        if(empty($score)){
            $redis->zAdd('material',time(),$material_json);
            $redis->hSet('material2url',md5($material_json),$ad_arr['url']);
        }
    }
}

function collectErrMaterial($ad_arr,$platform,$ad_name,$ad_shop,$redis=null){
    $type = stripos($ad_name,'splash')!==false?'splash':'list';
    $material_arr = array(
        'type'=>$type,
        'img'=>$ad_arr['img'],
        'txt'=>$ad_arr['content'],
        //'url'=>$ad_arr['url'],
        'ad_shop'=>$ad_shop,
    );
    $material_json = json_encode($material_arr);
    $score = $redis->zScore('material_err',$material_json);
    if(empty($score)){
        $redis->zAdd('material_err',time(),$material_json);
        $redis->hSet('material2url',md5($material_json),$ad_arr['url']);
    }
}

class Tools {
    public static function isBadAdvert($ad_arr){
        global $black_words,$black_domain,$black_img_url,$redis;

        //京东等正规商家不需要进行判断
        if(stripos($ad_arr['img'],'jd.com')===false && stripos($ad_arr['url'],'jd.com')===false && stripos($ad_arr['url'],'suning.com')===false && stripos($ad_arr['img'],'360buyimg.com')===false && stripos($ad_arr['img'],'meituan.net')===false && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50026/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50064/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50153/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50289/')!==false ) && !(stripos($ad_arr['img'],'.ipinyou.com/')!==false && stripos($ad_arr['img'],'/50333/')!==false ) ){

        }else{
            return false;
        }

        //携程， 苏宁，唯品会，天猫，超市，日产
        if(stripos($ad_arr['content'],'京东')!==false || stripos($ad_arr['content'],'携程')!==false || stripos($ad_arr['content'],'苏宁')!==false || stripos($ad_arr['content'],'唯品会')!==false || stripos($ad_arr['content'],'天猫')!==false || stripos($ad_arr['content'],'超市')!==false || stripos($ad_arr['content'],'日产')!==false || stripos($ad_arr['content'],'11.11')!==false || stripos($ad_arr['content'],'双十一')!==false){
            return false;
        }


//        if(!empty($black_words) || !empty($black_domain)){
            if(self::equalBadImgUrl($ad_arr['img'],$black_img_url) || self::containBadWord($ad_arr['content'],$black_words) || self::containBadUrl($ad_arr['url'],$black_domain)){
                $ad_arr['createtime'] = date("Y-m-d H:i:s");
                $material_err_log_key = 'material_err_log';
                if(stripos($ad_arr['type'],'图片')!==false){
                    $material_err_log_key = 'material_err_splash_log';
                }
                $m_err_count = $redis->lPush($material_err_log_key,json_encode($ad_arr));
                if($m_err_count>10000){
                    $redis->lTrim($material_err_log_key,0,2000);
                }
                return true;
            }else{
                return false;
            }
//        }
//        if(self::containBadWord($ad_arr['content']) || self::containBadUrl($ad_arr['url'])){
//            return true;
//        }else{
//            return false;
//        }
    }

    public static function equalBadImgUrl($url,$black_img_url=array()){
        if(in_array($url,$black_img_url)){
            return true;
        }else{
            return false;
        }
    }

    public static function containBadWord($str,$black_words=null){
        if(!empty($black_words)){
            foreach ($black_words as $word){
                if(stripos($str,$word)!==false){
                    return true;
                }
            }
            return false;
        }


        if(stripos($str,'乐视体育')!==false || stripos($str,'男人强壮的秘密')!==false || stripos($str,'老中医')!==false || stripos($str,'耐克')!==false || stripos($str,'阿迪')!==false || stripos($str,'彩票')!==false || stripos($str,'竞彩')!==false || stripos($str,'58同城')!==false || stripos($str,'股市')!==false || stripos($str,'炒股')!==false || stripos($str,'模特')!==false || stripos($str,'演员')!==false || stripos($str,'安慰')!==false || stripos($str,'猜球')!==false || stripos($str,'竞猜')!==false || stripos($str,'内幕')!==false || stripos($str,'魅莱')!==false || stripos($str,'孩子长不高')!==false || stripos($str,'孩子长不高')!==false || stripos($str,'偷窥')!==false || stripos($str,'肾')!==false || stripos($str,'性能力')!==false || stripos($str,'炒股')!==false || stripos($str,'离异')!==false || stripos($str,'赚钱')!==false){
            return true;
        }else{
            return false;
        }
    }

    public static function containBadUrl($url,$black_domain=null){
        if(!empty($black_domain)){
            foreach ($black_domain as $domain){
                if(stripos($url,$domain)!==false){
                    return true;
                }
            }
            return false;
        }

        if(
            stripos($url,'enyumy.com')!==false ||
            stripos($url,'fjnjsbw.com')!==false ||
            stripos($url,'kztydx.com')!==false ||
            stripos($url,'nike619.com')!==false ||
            stripos($url,'biddingx.com')!==false ||
            stripos($url,'rrzcp8.com')!==false ||
            stripos($url,'500zhongcai.com')!==false ||
            stripos($url,'caiqr.com')!==false ||
            stripos($url,'159cai.com')!==false ||
            stripos($url,'jc258.cn')!==false ||
            stripos($url,'365rich.com')!==false ||
            stripos($url,'lemicp.com')!==false ||
            stripos($url,'vipc.cn')!==false ||
            stripos($url,'500.com')!==false ||
            stripos($url,'okooo.com')!==false ||
            stripos($url,'8win.com')!==false ||
            stripos($url,'9w60.com')!==false ||
            stripos($url,'sensefun.com')!==false ||
            stripos($url,'106cai.com')!==false ||
            stripos($url,'cp.163.com')!==false ||
            stripos($url,'c16000.com')!==false ||
            stripos($url,'lecai.com')!==false ||
            stripos($url,'win310.com')!==false ||
            stripos($url,'qmcai.com')!==false ||
            stripos($url,'www008.com')!==false ||
            stripos($url,'9188.com')!==false ||
            stripos($url,'tigercai.com')!==false ||
            stripos($url,'yuecai365.com')!==false ||
            stripos($url,'meilai.3-he')!==false ||
            stripos($url,'leliuliang.cn')!==false ||
            stripos($url,'fengkuangtiyu.cn')!==false ||
            stripos($url,'xdert.cn')!==false ||
            stripos($url,'wodeju.com')!==false ||
            stripos($url,'tuia.cn')!==false ||
            stripos($url,'gz.51huaxun.cn')!==false
        ){
            return true;
        }else{
            return false;
        }
    }
}

function limitContent($content,$ad_name){
    $title = '';
    if(stripos($ad_name,'important')!==false || stripos($ad_name,'attention')!==false){
        if(versionCompare('android',484)===1 || versionCompare('ios',464)===1){
            $title = Onens::g_length($content)>50?Onens::g_substr($content,50,true):$content;
        }else{
            $title = Onens::g_length($content)>30?Onens::g_substr($content,30,true):$content;
        }
    }else{
        $title = Onens::g_length($content)>50?Onens::g_substr($content,50,true):$content;
    }
    return $title;
}


function versionCompare($target_platform,$target_version_val){
    $platform = empty($_REQUEST['platform'])?'android':$_REQUEST['platform'];
    $version_name = empty($_REQUEST['version_name'])?'':$_REQUEST['version_name'];
    $version_code = empty($_REQUEST['version_code'])?'':$_REQUEST['version_code'];
    $version_name = $platform=='android'?$version_name:$version_code;
    $version_val = (int)str_ireplace('.','',$version_name);
    if($platform != $target_platform){
        return false;
    }
    if($version_val>$target_version_val){
        return 1;
    }elseif ($version_val<$target_version_val){
        return -1;
    }else{
        return 0;
    }
}

/**
 * 字符串截取
 *https://data.zhibo8.cc/application/public/football/match/league/16.png
 * @author gesion<gesion@163.com>
 * @param string $str 原始字符串
 * @param int    $len 截取长度（中文/全角符号默认为 2 个单位，英文/数字为 1。
 *                    例如：长度 12 表示 6 个中文或全角字符或 12 个英文或数字）
 * @param bool   $dot 是否加点（若字符串超过 $len 长度，则后面加 "..."）
 * @return string
 */
class Onens {
    public static function g_substr($str, $len = 12, $dot = true) {
        $i = 0;
        $l = 0;
        $c = 0;
        $a = array();
        while ($l < $len) {
            $t = substr($str, $i, 1);
            if (ord($t) >= 224) {
                $c = 3;
                $t = substr($str, $i, $c);
                $l += 2;
            } elseif (ord($t) >= 192) {
                $c = 2;
                $t = substr($str, $i, $c);
                $l += 2;
            } else {
                $c = 1;
                $l++;
            }
            // $t = substr($str, $i, $c);
            $i += $c;
            if ($l > $len) break;
            $a[] = $t;
        }
        $re = implode('', $a);
        if (substr($str, $i, 1) !== false) {
            array_pop($a);
            ($c == 1) and array_pop($a);
            $re = implode('', $a);
            $dot and $re .= '...';
        }
        return $re;
    }

    public static function g_length($str) {
        $i = 0;
        $l = 0;

        $len = strlen($str);

        while ($i < $len) {
            $t = substr($str, $i, 1);
            if (ord($t) >= 224) {
                $c = 3;
                $l += 2;
            } elseif (ord($t) >= 192) {
                $c = 2;
                $l += 2;
            } else {
                $c = 1;
                $l++;
            }
            $i += $c;
        }

        return $l;
    }
}


