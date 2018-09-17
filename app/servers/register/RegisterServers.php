<?php
/**
 * @className：第三方注册用户管理接口
 * @description：添加会员  
 * @author:calfbbs技术团队
 * Date: 2018/03/29
 * Time: 下午3:25
 */
namespace App\servers\register;
use App\model\RegisterModel;
use App\model\UserModel;
use  Framework\library\Session;
use App\controller\Base;
class RegisterServers extends Base{
	public $appid;
    public $appsecret;
    public $type;

    public function __construct()
    {
        parent::__construct();
    }

    
    
   
    /**
     * 过滤Emoji
     */
    public function filterEmoji($str) {      
        if($str){ 
            $name = $str; 
            $name = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $name); 
            $name = preg_replace('/xE0[x80-x9F][x80-xBF]‘.‘|xED[xA0-xBF][x80-xBF]/S','?', $name); 
            $return = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#ie","",json_encode($name)));
            
        }else{ 
            $return = ''; 
        }     
        return $return; 
 
    } 

    /**
     *注册验证
     */
    public function siginin(){ 
        
        /**
         * 获取配置头像目录
         */      
        if(isset($_POST['avatar'])){
            $avatar_ori=$_POST['avatar'];
        }  
        $avatar=\framework\library\Conf::get('AVATAR','calfbbs');
        if(!$avatar){
            $avatar='default';
        }
        $jpg="boy".rand(1,10).".jpg";
        $_POST['avatar']="avatar/".$avatar."/".$jpg;
        $num=$this->randomkeys(3);
        if(!isset($_POST['username'])){
        	$_POST['username']=$this->filterEmoji($_POST['nickname']).$num;
    	}
        if(isset($_POST['type'])){
            if(($_POST['type']=='weixin_public') || ($_POST['type']=='weixin_open')){
                $_POST['type']='weixin';
                $type='weixin';
            }else{
                $type=$_POST['type'];
            }
        }
        $data=$this->post(url("api/user/adduser"),$_POST);

        if($data->code==1001){
            if(isset($avatar_ori)){
                $_POST['avatar']=$avatar_ori;
            }
            $_POST['uid']=$data->data;
            $data=$this->post(url("login/register/addRegister"),$_POST);            
            if($data->code==1001){
                /**
                 * access_token处理
                 */            
                if(!isset($_POST['type'])){
                    $_POST['type']='email';
                }else{
                    $_POST['type']='register';
                }
                $data=$this->post(url("api/user/login"),$_POST);
                //$this->logger("registers :\n".json_encode($data));
                if($data->code==1001){
                    $register=new RegisterModel();
                    $registerinfo=$register->getRegisterOne(['type'=>'uid','uid'=>$data->data->uid]);
                    $registerinfo['type']=$type;
                    $data->data->registerinfo=$registerinfo;

                    @$access_token=md5($this->randomkeys(6)+$data->data->uid);
                    $access_token=self::$session->set('access_token',$access_token);
                    $userinfo=self::$session->set($access_token,(array)$data->data);
                    $data->data="注册成功";

                }
            }
        }
        return $data;

        //echo json_encode($data);
    
    }
    
    /**
     * 生成6位字母+数字随机数
     * @param $length
     * @return null|string
     */
    public function randomkeys($length)
    {
        //$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $pattern = '1234567890';
        $key = null;
        for($i=0; $i<$length; $i++)
        {
            $key .= $pattern{mt_rand(0,9)};    //生成php随机数
        }
        return $key;
    }

    /**
     *http
     */
    public function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_URL, $url);    
        $res = curl_exec($curl);
        curl_close($curl);    
        return json_decode($res);
    }  
    
    // public function logger($content){
    //     $logsize=100000;
    //     $log="/www/web/default/log.txt";
    //     if(file_exists($log)&&filesize($log)>$logsize){
    //         unlink($log);
    //     }
    //     file_put_contents($log,date('H:i:s')." ".$content."\n",FILE_APPEND);
    // }
}

