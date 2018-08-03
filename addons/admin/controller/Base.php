<?php
/**
 * @className：基础类
 * @description：api调用类继承
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\admin\controller;
use Addons\admin\model\ApiModel;
use  Framework\library\View;
use  Framework\library\Session;
class Base extends ApiModel
{
    public static $userinfo;
    public static $session;
    use View;
    public function __construct()
    {
        if(!self::$session){
            self::$session=new Session();
        }

        if(!self::$userinfo){

        self::$userinfo=self::$session->get(self::$session->get('access_token'));

        }
        $this->validateLogin(); //验证是否登陆


        /**
         * 获取网站配置信息
         */
        $calfbbs = \Framework\library\conf::all('calfbbs');
        $title= $calfbbs['TITLE'] ? $calfbbs['TITLE'] : "calfbbs 经典开源社区系统,bbs论坛";
        $keywords=$calfbbs['KEYWORDS'] ? $calfbbs['KEYWORDS'] :"calfbbs 经典开源社区系统,bbs论坛";
        $description=$calfbbs['DESCRIPTION'] ? $calfbbs['DESCRIPTION'] : "calfbbs 经典开源社区系统,bbs论坛";
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->assign('userinfo',self::$userinfo);
        parent::__construct();
    }



    /** 跳转成功提示
     * @param        $url
     * @param string $message
     */
    public function success($url,$message="成功"){
        global $_G;
        $url=url("admin/base/showMessage","&message=".$message."&type=success"."&url=",false).urlencode($url);

        header("Location:".$url);
        exit;
    }


    /** 跳转失败提示
     * @param        $url
     * @param string $message
     */
    public function error($url,$message="失败"){
        global $_G;
        $url=url("admin/base/showMessage","&message=".$message."&type=error"."&url=",false).urlencode($url);
        header("Location:".$url);
        exit;
    }


    /**
     * 显示提示
     */
    public function showMessage(){
        global $_G;
        if(!isset($_GET['type'])){
            return;
        }
        $_GET['url']=urldecode($_GET['url']);
        $_GET['message']=urldecode($_GET['message']);
        $this->assign('url',$_GET['url']);
        $this->assign('message',$_GET['message']);
        if($_GET['type']=="error"){
            $this->display('common/error');
        }else{
            $this->display('common/success');
        }
    }

    /**
     * 登陆验证
     */
    public function validateLogin(){

        $login=$this->notValidateLogin();
        if($login==false){
            if(empty(self::$userinfo)){

                $this->error(url('admin/login/index'),'您当前还未登陆请先登陆');
            }
            if(self::$userinfo['status'] !=2){
                $this->error(url('admin/login/index'),'您当前身份不是管理员，请换个账号登陆');
            }

        }
    }
    /**
     * 不需要验证的控制器及方法
     */
    public function notValidateLogin(){
        $action=[
            'login'=>[
                'index','login'
            ],
            'base'=>['showMessage'],
        ];
        $login=false;
        foreach ($action as $c=>$a){
            if(is_array($a)){
                    foreach($a as $v){
                        if($c==C && $v==A){

                            $login=true;
                            continue;
                        }
                    }

                    if($login==true){
                        continue;
                    }
            }else{
                if($c==C && $a==A){
                    $login=true;
                    continue;
                }
            }

        }
       return $login;
    }

}