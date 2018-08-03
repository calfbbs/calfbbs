<?php
/**
 * @className：qq用户管理接口
 * @description：qq登录  
 * @author:calfbbs技术团队
 * Date: 2018/03/29
 * Time: 下午3:25
 */
namespace App\servers\register;
use App\servers\register\RegisterServers;
use App\model\RegisterModel;
use App\model\UserModel;
use  Framework\library\Session;

class QQServers extends RegisterServers{

	public function __construct($appid,$appsecret)
    {
    	parent::__construct();
    	//$this->appid="101471164";
		//$this->appsecret="5c7ce10d9ea24cbc61bdeba0a8773bb8";
        $this->appid=$appid;
        $this->appsecret=$appsecret;
		$this->type='qq';
    }
    /**
     * 解除绑定
     */
    public function unbind(){
        global $_GPC;
        $uid=$_GPC['uid'];
        $user=new UserModel();
        $data=$user->getUserOne($uid);
        if($data['email']!=''){
            $register=new RegisterModel();
            $data=$register->delRegister(['uid'=>$uid,'type'=>$this->type]);
            if($data->code==1001){
                $this->success(url("app/users/set",['uid'=>$uid]), '解除绑定成功。');
            }
        }else{          
            $this->error(url("app/users/set",['uid'=>$uid]), 'email为空，不能解除绑定。');            
        }       
    }
    /**
     * 绑定
     */
    public function bind(){
        global $_GPC;
        //$redirect_uri="http://www.wuhaomouse.com/index.php?m=app&c=registers&a=bind&uid=".$_GPC['uid']."&type=".$this->type;
        $redirect_uri=url("app/registers/bind",['uid'=>$_GPC['uid'],'type'=>$this->type]);
        @$code=$_GET['code'];
        if($code){
        	$state=self::$session->get('qq_state');        	
            //$this->logger("login qq_state:".$state);
            self::$session->del('qq_state');
            if($state==$_GET['state']){
	            $uid=$_GPC['uid']; 
	            $openid=$this->getOpenid($code,$redirect_uri);
	            $content=$this->getUserinfo($openid);
	            $content->openid=$openid; 
	            $wdata=array('uid'=>$uid,'openid'=>$content->openid,'nickname'=>$content->nickname,'type'=>$this->type);
	            $register=new RegisterModel();
	            $data=$register->getRegisterOne(['type'=>'uid','uid'=>$uid]);
	            if($data){
	            	$udata['openid']=$openid;
	            	$udata['nickname']=$content->nickname;
	            	$udata['type']=$this->type;
	            	$udata['id']=$data['id'];	
		            $wdata=$this->post(url("login/register/updateregister"),$udata);
		            if($wdata->code==1001){ 
		                $this->success(url("app/users/set",['uid'=>$uid]), '绑定成功。');         
		            }
	        	}else{	        		
	            	
                    $data=$register->getRegisterOne(['type'=>'qq','openid'=>$openid]);
                    if($data){
                        $this->error(url("app/users/set",['uid'=>$uid]), '此qq已绑定了其他账号。');
                    }else{
    	        		$wdata=$this->post(url("login/register/addregister"),$wdata);
    		            if($wdata->code==1001){ 
    		                $this->success(url("app/users/set",['uid'=>$uid]), '绑定成功。');         
    		            } 
                    }
	        	}
	        }else {
                 $this->error(url("app/users/set",['uid'=>$uid]),"The state does not match. You may be a victim of CSRF.");
            }
        }else{
            $this->qrCode($redirect_uri);
        }
    }
    /**
     * 登录
     */
    public function login()
    {
        //$redirect_uri="http://www.wuhaomouse.com/index.php?m=app&c=registers&a=login&type=".$this->type;
        $redirect_uri=url("app/registers/login",['type'=>$this->type]);
        @$code=$_GET['code'];
        if($code){
            $state=self::$session->get('qq_state');
            self::$session->del('qq_state');
            if($state==$_GET['state']){
                $openid=$this->getOpenid($code,$redirect_uri);
                $content=$this->getUserinfo($openid);
                $content->openid=$openid;
                $register=new RegisterModel();
		$data=$register->getRegisterOne(['type'=>$this->type,'openid'=>$content->openid]);

                if($data){
                    $uid=$data['uid'];
                    $user_m=new UserModel();
                    $data=$user_m->getUserOne($uid);
                    if($data){
                        $user['username']=$data['username'];
                        $user['type']='register';
                        $user['uid']=$uid;
                        $data=$this->post(url("api/user/login"),$user);
                        if($data->code==1001){
                            @$access_token=md5($this->randomkeys(6)+$data->data->uid);
                            $access_token=self::$session->set('access_token',$access_token);
                            $userinfo=self::$session->set($access_token,(array)$data->data);
                            $this->success(url("app/index/index"), '用户已注册过，登录成功'); 
                        }
                    }
                }else{
                    $content->type=$this->type;
                    $this->assign('content',$content);
                    $this->display('registers/signup');
                }
            }else {
                 $this->error(url("app/index/index"),"The state does not match. You may be a victim of CSRF.");
            }
        }else{
            $this->qrCode($redirect_uri);
        }
    }
    /**
     **qq授权登录
     */
    public function qrCode($redirect_uri){
        $state=self::$session->set('qq_state',md5(uniqid(rand(), TRUE)));  
        //$this->logger("qrcode qq_state".$state);
        //$this->logger("qrcode redirect_uri".$redirect_uri);   
        
        $oauth2_url = "https://graph.qq.com/oauth2.0/authorize?client_id=".$this->appid."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=get_user_info&state=".$state; 
               
        header("location: $oauth2_url");
        exit();
    }
    /**
     * qqopenid
     */
    public function getOpenid($code,$redirect_uri){
    	$accessToken=self::$session->get('qq_accessToken');
        $refresh_token=self::$session->get('qq_refresh_token');
        if($accessToken){
            $oauth2_code="https://graph.qq.com/oauth2.0/token?client_id=" . $this->appid . "&client_secret=" . $this->appsecret. "&grant_type=refresh_token&refresh_token=".$refresh_token;
            $response = file_get_contents($oauth2_code);
            $params = array();
            parse_str($response,$params);
            
            $accessToken=self::$session->set('qq_accessToken',$params['access_token']);
            $refresh_token=self::$session->set('qq_refresh_token',$params['refresh_token']);
        }else{
            $oauth2_code = "https://graph.qq.com/oauth2.0/token?client_id=" . $this->appid . "&client_secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code&redirect_uri=".urlencode($redirect_uri);
            $response = file_get_contents($oauth2_code);
            $params = array();
            parse_str($response,$params);
            $accessToken=self::$session->set('qq_accessToken',$params['access_token']);
            $refresh_token=self::$session->set('qq_refresh_token',$params['refresh_token']);
        }
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$accessToken;
        $response  = file_get_contents($graph_url);
        if (strpos($response, "callback") !== false)
	     {
	        $lpos = strpos($response, "(");
	        $rpos = strrpos($response, ")");
	        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
	        $msg = json_decode($response);
	        if (isset($msg->error))
	        {	           
	           $this->error(url("app/index/index"),$msg->error.$msg->error_description);
	        }
	        $openid=$msg->openid;
	     }
        
        return $openid;
    }
    /**
     * qquserinfo
     */
    public function getUserinfo($openid){
    	$accessToken=self::$session->get('qq_accessToken');
        $refresh_token=self::$session->get('qq_refresh_token');
        $tokenUrl = "https://graph.qq.com/user/get_user_info?access_token=" . $accessToken . "&oauth_consumer_key=".$this->appid ."&openid=" . $openid ;
        $content = $this->httpGet($tokenUrl);
        return $content;
    }

}
