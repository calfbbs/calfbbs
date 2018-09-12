<?php
/**
 * @className：基础类
 * @description：api调用类继承
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace App\controller;
use App\model\ApiModel;
use  Framework\library\View;
use  Framework\library\Session;
use App\model\NavModel;
class Base extends ApiModel
{
    public static $userinfo="";
    public static $session;
    public static $index;

    use View;
    public function __construct()
    {   global $_G;
        parent::__construct();

        if(!self::$session){
            self::$session=new Session();
        }

        if(!self::$userinfo){
            self::$userinfo=self::$session->get(self::$session->get('access_token'));
        }

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
        $this->assign('this_url',$http_type."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

        $this->validateInstall();//验证是否安装
        $calfbbs = \Framework\library\conf::all('calfbbs');
        $title= $calfbbs['TITLE'] ? $calfbbs['TITLE'] : "calfbbs 经典开源社区系统,bbs论坛";
        $keywords=$calfbbs['KEYWORDS'] ? $calfbbs['KEYWORDS'] :"calfbbs 经典开源社区系统,bbs论坛";
        $description=$calfbbs['DESCRIPTION'] ? $calfbbs['DESCRIPTION'] : "calfbbs 经典开源社区系统,bbs论坛";
        $icp=$calfbbs['ICP'] ? $calfbbs['ICP'] : "黔ICP备17009723号";
        $copyright=$calfbbs['COPYRIGHT'] ? $calfbbs['COPYRIGHT'] : "calfbbs 版权所有";
        $logo=$calfbbs['LOGO'] ? $calfbbs['LOGO'] : "logo.png";
        $nav = new NavModel();
        $suffix=\Framework\library\conf::get('IDENX_SUFFIX',"route");

        if($suffix){
            define("SUFFIX","");

        }else{
            define("SUFFIX","/index.php/");
        }

        /**
         * 获取导航列表
         */
        $navList=$nav->getNavList();
        $this->assign('navList',$navList);
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->assign('icp',$icp);
        $this->assign('copyright',$copyright);
        $this->assign('logo',$logo);
        $this->assign('userinfo',self::$userinfo);

    }

    /**
     * 登陆验证
     */
    public function validateLogin(){

        /**
         * 不需要验证的控制器及方法
         */

        $login=$this->notValidateLogin();


        if(!self::$userinfo){
            self::$userinfo=self::$session->get(self::$session->get('access_token'));
        }

        if($login==false){
            if(empty(self::$userinfo)){

                $this->error(url('app/login/index'),'您当前还未登陆请先登陆');
            }
        }

    }



    /**
     * 安装验证
     */

    public function validateInstall(){
        //判断是否已安装
        if(!is_file(CALFBB."/data/install.lock") && is_file(CALFBB."/addons/install/controller/Index.php")){

            @header("location:".url('install/index/index'));
        }
    }

    /**
     * 不需要验证的控制器及方法
     */
    public function notValidateLogin(){
        $action=[
            'login'=>[
                'index','login','signup','siginin','captcha','logout'
            ],
            'users'=>[
                'home'
            ]
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


    /**
     * 获取分类列表
     */
    public function column(){
        global $_G;
        $data=$this->get(url("api/classify/getClassifylist"));
        if($data->code==1001 && $data->data){
            return  $data->data;
        }else{
            return [];
        }
    }



    /**
     * 生成6位字母+数字随机数
     * @param $length
     * @return null|string
     */
    public function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = null;
        for($i=0; $i<$length; $i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }

    /** 跳转成功提示
     * @param        $url
     * @param string $message
     */
    public function success($url,$message="成功"){
        global $_G;
        $url=url("app/base/showMessage","&message=".$message."&type=success"."&url=",false).urlencode($url);

        header("Location:".$url);
        exit;
    }


    /** 跳转失败提示
     * @param        $url
     * @param string $message
     */
    public function error($url,$message="失败"){
        global $_G;
        $url=url("app/base/showMessage","&message=".$message."&type=error"."&url=",false).urlencode($url);
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

}