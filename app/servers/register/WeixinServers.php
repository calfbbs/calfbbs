<?php
/**
 * @className：微信用户管理接口
 * @description：微信登录  
 * @author:calfbbs技术团队
 * Date: 2018/03/29
 * Time: 下午3:25
 */
namespace App\servers\register;
use App\servers\register\RegisterServers;
use App\model\RegisterModel;
use App\model\UserModel;
use  Framework\library\Session;
class WeixinServers extends RegisterServers{

	public function __construct($appid,$appsecret,$weixin_type)
    {
    	parent::__construct();
    	//$this->appid="wxd610cd2dee5f422c";
		//$this->appsecret="222b3b6670613da911773caa0a752a6d";
        $this->appid=$appid;
        $this->appsecret=$appsecret;
		$this->type='weixin';
        $this->weixin_type=$weixin_type;
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
        $redirect_uri=url("app/registers/bind",['uid'=>$_GPC['uid'],'type'=>$this->weixin_type]);
        @$code=$_GET['code'];
        if($code){
            $uid=$_GPC['uid']; 
            $openid=$this->getOpenid($code);
            $content=$this->getUserinfo($openid);
            $wdata=array('uid'=>$uid,'openid'=>$content->openid,'nickname'=>$content->nickname,'avatar'=>$content->headimgurl,'type'=>$this->type);
            $register=new RegisterModel();
            $data=$register->getRegisterOne(['type'=>'uid','uid'=>$uid]);
            if($data){
            	$udata['openid']=$openid;
            	$udata['nickname']=$content->nickname;
            	$udata['avatar']=$content->headimgurl;
            	$udata['type']=$this->type;
            	$udata['id']=$data['id'];	
	            $wdata=$this->post(url("login/register/updateregister"),$udata);
	            if($wdata->code==1001){ 
	                //$this->success(url("app/users/set",['uid'=>$uid]), '绑定成功。');  
	                header("location: ".url("app/index/index"));
                    exit();       
	            }
        	}else{
                $data=$register->getRegisterOne(['type'=>'weixin','openid'=>$openid]);
                if($data){
                    $this->error(url("app/users/set",['uid'=>$uid]), '此微信已绑定了其他账号。');
                }else{
            		$wdata=$this->post(url("login/register/addregister"),$wdata);
    	            if($wdata->code==1001){ 
    	                //$this->success(url("app/users/set",['uid'=>$uid]), '绑定成功。');   
    	                header("location: ".url("app/index/index"));
                    	exit();       
    	            } 
                }
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
        $redirect_uri=url("app/registers/login",['type'=>$this->weixin_type]);
        @$code=$_GET['code'];
        if($code){
            $state=self::$session->get('weixin_state');
            self::$session->del('weixin_state');
            if($state==$_GET['state']){
                $openid=$this->getOpenid($code);
                $content=$this->getUserinfo($openid);
                $content->openid=$openid;    
                $content->avatar=$content->headimgurl;
                $register=new RegisterModel();
                $registerinfo=$register->getRegisterOne(['type'=>$this->type,'openid'=>$content->openid]);
                
                if($registerinfo){
                	$registerinfo['type']=$this->type;
                    $uid=$registerinfo['uid'];
                    $user_m=new UserModel();
                    $data=$user_m->getUserOne($uid);
                    if($data){
                        $user['username']=$data['username'];
                        $user['type']='register';
                        $user['uid']=$uid;
                        $data=$this->post(url("api/user/login"),$user);
                        if($data->code==1001){
                        	$data->data->registerinfo=$registerinfo;
                            @$access_token=md5($this->randomkeys(6)+$data->data->uid);
                            $access_token=self::$session->set('access_token',$access_token);
                            $userinfo=self::$session->set($access_token,(array)$data->data);
                            //$this->success(url("app/index/index"), '用户已注册过，登录成功'); 
                            header("location: ".url("app/index/index"));
                            exit();
                        }
                    }
                }else{
                    $content->type=$this->weixin_type;
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
     * 微信登陆二维码
     */
    public function qrCode($redirect_uri){  
        $state=self::$session->set('weixin_state',md5(uniqid(rand(), TRUE)));
        if($this->weixin_type=='weixin_open'){//开放平台
            $oauth2_url = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->appid."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_login&state=".$state."#wechat_redirect";
        }else{//weixin_pubic 微信公众号授权
            $oauth2_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appid . "&redirect_uri=" . urlencode($redirect_uri) . "&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";          
        }
        header("location: $oauth2_url");
        exit();
    }
    
    /**
     * 微信openid
     */
    public function getOpenid($code){
        $accessToken=self::$session->get('weixin_accessToken');
        $refresh_token=self::$session->get('weixin_refresh_token');
        if($accessToken){
            $oauth2_code="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $this->appid . "&grant_type=refresh_token&refresh_token=".$refresh_token;
            $res=$this->httpGet($oauth2_code); 
            $accessToken=self::$session->set('weixin_accessToken',$res->access_token);
            $refresh_token=self::$session->set('weixin_refresh_token',$res->refresh_token);
        }else{
            $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code";
            $res=$this->httpGet($oauth2_code);
            $accessToken=self::$session->set('weixin_accessToken',$res->access_token);
            $refresh_token=self::$session->set('weixin_refresh_token',$res->refresh_token);
        }
        $openid=$res->openid;
        return $openid;
    }
    /**
     * 微信userinfo
     */
    public function getUserinfo($openid){
    	$accessToken=self::$session->get('weixin_accessToken');
        $refresh_token=self::$session->get('weixin_refresh_token');
        $tokenUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $accessToken . "&openid=" . $openid . "&lang=zh_CN";
        $content = $this->httpGet($tokenUrl);
        return $content;
    }

}
