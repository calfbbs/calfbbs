<?php
/**
 * @className：微信登录控制器
 * @description：微信注册，登录，绑定，解绑定
 * @author:calfbb技术团队
 * Date: 2018/03/29
 */
namespace App\controller;

use App\servers\register\WeixinServers;
use App\servers\register\QQServers;
use  Framework\library\Session;
class Registers
{	
    static public $conf = array();
    /**
     * 获取配置信息
     */
    public function getConf($modules,$file="weixin"){
        $route = \Framework\library\conf::all('route');
        $conf = CALFBB.'/'.$route['DEFAULT_ADDONS'].'/'.$modules."/config/".$file.".conf";

        
        if(is_file($conf)) {
            if(isset(self::$conf[$file])) {
                return self::$conf[$file];
            } else {  
                self::$conf[$file] = include $conf;
                return self::$conf[$file];                
            }
        }
        return false;
    }
    /**
     * 登录
     */
    public function login()
    { 
        global $_GPC;
        $config=$this->getConf('login',$_GPC['type']);
        $appid=$config['appid'];
        $appsecret=$config['appsecret'];
        if($_GPC['type']=='weixin'){
            $servers=new WeixinServers($appid,$appsecret);
            $servers->login();
        }
        if($_GPC['type']=='qq'){
            $servers=new QQServers($appid,$appsecret);
            $servers->login();
        }
        
    }   
    public function siginin(){
        global $_GPC;
        $config=$this->getConf('login',$_GPC['type']);
        $appid=$config['appid'];
        $appsecret=$config['appsecret'];
        if($_GPC['type']=='weixin'){
            $servers=new WeixinServers($appid,$appsecret);
            $data=$servers->siginin();
        }
        if($_GPC['type']=='qq'){
            $servers=new QQServers($appid,$appsecret);
            $data=$servers->siginin();
        }
        echo json_encode($data);
    }
    public function bind(){
        global $_GPC;
        $config=$this->getConf('login',$_GPC['type']);
        $appid=$config['appid'];
        $appsecret=$config['appsecret'];
        if($_GPC['type']=='weixin'){
            $servers=new WeixinServers($appid,$appsecret);
            $servers->bind();
        }
        if($_GPC['type']=='qq'){
            $servers=new QQServers($appid,$appsecret);
            $servers->bind();
        }
    }
    public function unbind(){
        global $_GPC;
        $config=$this->getConf('login',$_GPC['type']);
        $appid=$config['appid'];
        $appsecret=$config['appsecret'];
        if($_GPC['type']=='weixin'){
            $servers=new WeixinServers($appid,$appsecret);
            $servers->unbind();
        }
        if($_GPC['type']=='qq'){
            $servers=new QQServers($appid,$appsecret);
            $servers->unbind();
        }
    }
     
}
