<?php
/**
 * Created by PhpStorm.
 * User: zhaoyuanming
 * Date: 15/8/20
 * Time: 上午10:12
 */

use Phalcon\Mvc\Controller;


class AdvertController extends Controller
{
    private $username = '';
    private $uid = 0;

    public function initialize(){
        $auth = $this->session->get('auth');
        //此控制器全局都需要管理员权限
        $this->checkAuth('管理员');
        if(!empty($auth)){
            $this->username = $auth['name'];
            $this->uid = $auth['id'];
        }
    }

    public function indexAction()
    {
        $key = 'sUEYjk))si001';
        $t = time();
        $c = 'look';
        $sign = md5($key.$t.$c);
        echo $this->curlHelper->get('http://120.55.180.105:8091/p.php?c='.$c.'&sign='.$sign.'&t='.$t);
    }

    public function listAction(){
        $list = MobileAdverts::find();
        $this->view->advert_arr = $list->toArray();
    }

    public function editorAction($id=0){
        //权限判断
        if(!$this->checkAuth('管理员'))return;

        if(!empty($id)){
            $id = (int)$id;
            $bean = MobileAdverts::findFirst('id = '.$id);
            $this->view->bean = $bean;
        }

        include_once __DIR__.'/../../houtai/manage/config/labels.php';
        $this->view->nba_labels = getLabels('nba');
        $this->view->zuqiu_labels = getLabels('zuqiu');
        $this->view->all_labels = array_merge(array('全部标签','|'),getLabels('nba'),getLabels('zuqiu'));

        $this->view->is_admin = $this->isAdmin();
    }

    public function deleteAction($id=0){
        //权限判断
        if(!$this->checkAuth('管理员'))return;

        $id = (int)$id;
        $bean = MobileAdverts::findFirst($id);

        if(!empty($id) && $bean != false){
            if ($bean->delete() == false) {
                foreach ($bean->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $this->flash->success('成功删除.');
            }
        }else{
            $this->flash->error('请指定条目');
            $this->response->redirect('advert/list');
            return false;
        }
    }

    public function saveAction(){
        if(!$this->checkAuth('管理员'))return;
        if ($this->request->isPost()) {
            $id = $this->request->hasPost("id")?(int)$this->request->getPost('id'):0;
            $platform = $this->request->getPost('platform');
            $placement = $this->request->getPost('placement');
            $type = $this->request->getPost('type');
            $showTimes = $this->request->getPost('showTimes');
            $duration = $this->request->getPost('duration');
            $name = $this->request->getPost('name');
            $start = $this->request->getPost('start');
            $end = $this->request->getPost('end');
            $status = $this->request->getPost('status');
            $img = $this->request->getPost('img');
            $url = $this->request->getPost('url');
            $module = $this->request->getPost('module');
            $match_type = $this->request->getPost('match_type');
            $force_care = $this->request->getPost('force_care');
            $label = empty($this->request->getPost('label'))?'':implode(',',$this->request->getPost('label'));


            if(!empty($id)){
                $bean = MobileAdverts::findFirst('id = '.$id);
            }
            if(empty($id) || empty($bean)){
                $bean = new MobileAdverts();
                $bean->creater = $this->username;
            }else{
                $bean->updater = $this->username;
            }

            $bean->label = $label;

//            $bean->platform = $platform;
//            $bean->placement = $placement;
//            $bean->type = $type;
//            $bean->showTimes = $showTimes;
//            $bean->duration = $duration;
//            $bean->name = $name;
//            $bean->start = $start;
//            $bean->end = $end;
//            $bean->status = $status;
//            $bean->img = $img;
//            $bean->url = $url;

            if($bean->save($_POST,array('platform','placement','type','showTimes','duration','name','start','end','status','title','img','url','module','match_type','force_care','monopolize','show_ping','click_ping')) == false){
                foreach ($bean->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }else{
                $this->flash->success('保存成功');
            }
        }
    }

    public function batchsaveAction(){
        if ($this->request->isPost()) {
            $start = $this->request->getPost('start');
            $end = $this->request->getPost('end');
            $status = $this->request->getPost('status');
            if(empty($start) || empty($end)){
                $this->flash->error("时间段不能为空，请重新设置。");
                return;
            }

            $ids = $this->request->getPost("ids");
            $id_arr = explode(',',$ids);
            foreach ($id_arr as $id){
                $id = (int)$id;
                if(empty($id)){
                    echo '<p>empty</p>';
                    continue;
                }

                $bean = MobileAdverts::findFirst('id = '.$id);
                $bean->start = $start;
                $bean->end = $end;
                $bean->status = $status;
                if($bean->save()== false){
                    foreach ($bean->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('ID'.$bean->id.'('.$bean->name.')保存成功');
                }
            }
        }
    }

    public function updateApiAction(){
        $list = MobileAdverts::findByPlacement('开机画面');
        $advert_arr = $list->toArray();

        $material_arr = array();
//        foreach($advert_arr as $a){
//            if(!empty($a['img'])){
//                $material_arr[] = array(
//                    'id'=>$a['id'],
//                    'img'=>$a['img'],
//                );
//            }
//        }


        $json_str = json_encode($material_arr);
        $file_path = APP_PATH.'../m.zhibo8.cc/activities/material/json.htm';
        $this->fileHelper->writefile($json_str,$file_path);

        $php_str = '<?php $json_str=\''.json_encode($advert_arr).'\';';
        $file_path = APP_PATH.'../m.zhibo8.cc/activities/material/json.php';
        $this->fileHelper->writefile($php_str,$file_path);

        $this->flash->success('<pre>'.print_r($advert_arr).'</pre>');
        $this->flash->success('已更新,http://m.zhibo8.cc/activities/material/json.htm.');

        $this->updateCacheAction();
    }

    public function updateCacheAction(){
        $list = MobileAdverts::find(array(
            '1 AND status = :status: AND start <= :now: AND [end]>=:now:',
            "bind"=>array('status'=>'enable','now'=>date("Y-m-d H:i:s")),
        ));

        $advert_arr = $list->toArray();

        $redis = new Redis();
        $redis->connect('46cb88787eb24653.m.cnhza.kvstore.aliyuncs.com',6379);
        if ($redis->auth('46cb88787eb24653:wuU098SH2aslk') == false) {
            die($redis->getLastError());
        }

        $key_arr = array(
            '开机画面'=>'main_splash',
            '首页重要'=>'main_important',
            '首页新闻'=>'main_news',
            '底部新闻'=>'news_list',
            '底部视频'=>'video_list',
            '首页弹窗'=>'main_popup',
            '商城弹窗'=>'mall_popup',
            '新闻内页'=>'news_neiye',
            '直播内页'=>'zhibo_banner',
            '关注信息流'=>'main_attention',
            '彩民间置顶'=>'room_caimin',
            '球迷间置顶'=>'room_qiumi',
        );
        $key_with_advert_arr = array();
        $advert_data_arr = array();

        foreach ($advert_arr as $advert){
            $keys = array();
            if($advert['platform']=='安卓'){
                if(!empty($key_arr[$advert['placement']])){
                    $keys[] = $key_arr[$advert['placement']].'_android';
                }
            }elseif ($advert['platform']=='iOS'){
                if(!empty($key_arr[$advert['placement']])){
                    $keys[] = $key_arr[$advert['placement']].'_ios';
                }
            }elseif ($advert['platform']=='全部'){
                if(!empty($key_arr[$advert['placement']])){
                    $keys[] = $key_arr[$advert['placement']].'_android';
                    $keys[] = $key_arr[$advert['placement']].'_ios';
                }
            }

            foreach ($keys as $k){
                $ad_data = array();
                $ad_data['id'] = empty($advert['id'])?'':$advert['id'];
                $ad_data['type'] = empty($advert['type'])?'':$advert['type'];
                $ad_data['name'] = empty($advert['name'])?'':$advert['name'];
                $ad_data['showTimes'] = empty($advert['showTimes'])?'':$advert['showTimes'];
                $ad_data['duration'] = empty($advert['duration'])?'':$advert['duration'];
                $ad_data['content'] = empty($advert['title'])?'':$advert['title'];
                $ad_data['img'] = empty($advert['img'])?'':$advert['img'];
                $ad_data['url'] = empty($advert['url'])?'':$advert['url'];
                $ad_data['label'] = empty($advert['label'])?'':$advert['label'];
                $ad_data['match_type'] = empty($advert['match_type'])?'':$advert['match_type'];
                $ad_data['force_care'] = empty($advert['force_care'])?'':$advert['force_care'];
                $ad_data['ua_ping_url'] = empty($advert['show_ping'])?'':$advert['show_ping'];
                $ad_data['ua_click_ping_url'] = empty($advert['click_ping'])?'':$advert['click_ping'];
                $ad_data['module'] = empty($advert['module'])?'':$advert['module'];
                $ad_data['monopolize'] = empty($advert['monopolize'])?'':$advert['monopolize'];

                $advert_data_arr[$k][] = json_encode($ad_data);

//                $redis->hSet($k,'id',empty($advert['id'])?'':$advert['id']);
//                $redis->hSet($k,'type',empty($advert['type'])?'':$advert['type']);
//                $redis->hSet($k,'name',empty($advert['name'])?'':$advert['name']);
//                $redis->hSet($k,'showTimes',empty($advert['showTimes'])?'':$advert['showTimes']);
//                $redis->hSet($k,'duration',empty($advert['duration'])?'':$advert['duration']);
//                $redis->hSet($k,'content',empty($advert['title'])?'':$advert['title']);
//                $redis->hSet($k,'img',empty($advert['img'])?'':$advert['img']);
//                $redis->hSet($k,'url',empty($advert['url'])?'':$advert['url']);
//                $redis->hSet($k,'label',empty($advert['label'])?'':$advert['label']);
//                $redis->hSet($k,'match_type',empty($advert['match_type'])?'':$advert['match_type']);
//                $redis->hSet($k,'force_care',empty($advert['force_care'])?'':$advert['force_care']);
//                $redis->hSet($k,'ua_ping_url',empty($advert['show_ping'])?'':$advert['show_ping']);
//                $redis->hSet($k,'ua_click_ping_url',empty($advert['click_ping'])?'':$advert['click_ping']);
//                $redis->hSet($k,'module',empty($advert['module'])?'':$advert['module']);
            }


//            foreach ($keys as $n =>$k){
//                $data = $redis->hGetAll($k);
//                $advert_data_arr[$k][] = json_encode($data);
////                echo '<p>'.$n.'=='.$k.$advert['id'].'</p>';
////                echo '<pre>';
////                print_r($data);
////                echo '</pre>';
////
////                if(!in_array($k,$key_with_advert_arr)){
////                    $redis->del('set_'.$k);
////                }
////                $redis->sAdd('set_'.$k,json_encode($data));
//            }

            $key_with_advert_arr = array_merge($key_with_advert_arr,$keys);
        }

//        return;

        foreach ($advert_data_arr as $pos=>$advert_pos_data_arr){
            $online_pos_data_arr = $redis->sMembers('set_'.$pos);
            if($pos=='main_important_android'){
                echo '<pre>';
                print_r($online_pos_data_arr);
                echo '</pre>';
                echo '<pre>';
                print_r($advert_pos_data_arr);
                echo '</pre>';
                $info = array_diff($advert_pos_data_arr,$online_pos_data_arr);
                echo '<pre>';
                print_r($info);
                echo '</pre>';
            }
//            echo '<pre>';
//            print_r($online_pos_data_arr);
//            echo '</pre>';
//            echo '<pre>';
//            print_r($advert_pos_data_arr);
//            echo '</pre>';
            if(!array_diff($online_pos_data_arr,$advert_pos_data_arr) && !array_diff($advert_pos_data_arr,$online_pos_data_arr)){
                echo '<p>same data on '.$pos.'</p>';
                continue;
            }else{
                echo '<p>updating data on '.$pos.'</p>';
                $redis->del('set_'.$pos);
                foreach ($advert_pos_data_arr as $data_json){
                    $redis->sAdd('set_'.$pos,$data_json);
                }
            }
        }


        foreach ($key_arr as $item){
            $key_ios = $item.'_ios';
            $key_android = $item.'_android';
            if(!in_array($key_ios,$key_with_advert_arr)){
                echo '<p>ios  without advert =>'.$key_ios.'</p>';
//                $redis->del($key_ios);
                $redis->del('set_'.$key_ios);
            }
            if(!in_array($key_android,$key_with_advert_arr)){
                echo '<p>android  without advert =>'.$key_android.'</p>';
//                $redis->del($key_android);
                $redis->del('set_'.$key_android);
            }
        }
    }

    public function queryScheduleAction($platform,$startDate,$endDate,$placement){
        if($platform=='全部'){
            $list = MobileAdverts::find(array(
                ' ((start <= :start: and [end]>=:start:) or (start <= :end: and [end]>=:end:)) and placement = :placement: and status = "enable"',
                'bind' => array('start' => $startDate,'end' => $endDate, 'placement'=>$placement )
            ));
        }else{
            $list = MobileAdverts::find(array(
                '(platform = :platform: or platform = "全部") and ((start <= :start: and [end]>=:start:) or (start <= :end: and [end]>=:end:)) and placement = :placement: and status = "enable"',
                'bind' => array('platform' => $platform, 'start' => $startDate,'end' => $endDate, 'placement'=>$placement )
            ));
        }

        $retArr = $list->toArray();
        $arr = array('num'=>count($retArr),'data'=>$retArr);
        echo json_encode($arr);
        exit();
    }


    public function dspEditorAction(){
        $dsp_list = array('maiguan'=>'迈观','xunfei'=>'讯飞','inmobi'=>'inmobi','kuaiyou'=>'快友',);
        $models = array('splash'=>'开屏','list'=>'信息流','neiye'=>'内页');
        $platforms = array('ios'=>'ios','android'=>'android');
        $this->view->dsp_list = $dsp_list;
        $this->view->models = $models;
        $this->view->platforms = $platforms;

        $redis = new Redis();
        $redis->connect('46cb88787eb24653.m.cnhza.kvstore.aliyuncs.com',6379);
        if ($redis->auth('46cb88787eb24653:wuU098SH2aslk') == false) {
            die($redis->getLastError());
        }
        $dsp_weight = $redis->hGetAll('dsp_weight');
        $this->view->dsp_weight = $dsp_weight;
    }

    public function saveDspAction(){
        $redis = new Redis();
        $redis->connect('46cb88787eb24653.m.cnhza.kvstore.aliyuncs.com',6379);
        if ($redis->auth('46cb88787eb24653:wuU098SH2aslk') == false) {
            die($redis->getLastError());
        }

        $dsp_arr = $this->request->getPost();
        foreach ($dsp_arr as $key=>$value){
            $redis->hSet('dsp_weight',$key,(int)$value);
//            echo '<p>'.$key.':'.$value.'</p>';
        }
        $this->flash->success('保存成功 <a href="/advert/dspEditor">点击返回</a>');
    }

    public function isAdmin(){
        $auth = $this->session->get('auth');
        if(Auths::hasAuth('管理员',$auth['id'])){
            return true;
        }else{
            return false;
        }
    }

    public function checkAuth($local_page){
        //权限判断
        $auth = $this->session->get('auth');
        //$local_page = $p_group.'位置';
        if(!Auths::hasAuth($local_page,$auth['id'])){
            $this->flash->error('没有权限 -- ['.$local_page.']');
            $this->view->enable = false;
            //$this->view->setRenderLevel(Phalcon\Mvc\View::LEVEL_NO_RENDER);
            return false;
        }
        $this->view->enable = true;
        return true;
    }

}