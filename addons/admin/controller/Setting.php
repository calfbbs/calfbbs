<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/7
 * Time: 下午7:05
 */

namespace Addons\admin\controller;
use  Framework\library\View;
use Addons\admin\controller\Base;
use  Addons\admin\model\UpdateModel;
use Framework\library\File;
class Setting extends Base
{
    static $master=false;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  显示在线更新
     */
    public function updateShow(){
        global $_GPC,$_G;
        $update=new \Addons\admin\model\UpdateModel;
        $version=$update->getVersion();
        $ping=json_decode($update->ping($version['version']),true);
        $version=$update->getVersion();
        if(@$ping['code']==0){
            $this->assign('data',@$ping);
        }else{
            $this->assign('data','');
        }



        $this->assign('version',$version['version']);
        $this->display('setting/updateshow');

    }

    /**
     * 检测版本
     */

    public function checkVersion(){
        $update=new \Addons\admin\model\UpdateModel;
        $version=$update->getVersion();

        $getUpFileList=$update->getUpFileList($version['version']['serverListApi'],$version['version']);
        $serverVersion=$update->serverVersion($version['version']['serverVersionApi'],$version['version']);
        $check=json_decode($getUpFileList,true);
        $serverVersion=json_decode($serverVersion,true);

        $this->assign('check',$check);
        $this->assign('version',$version['version']);
        $this->assign('serverVersion',$serverVersion);
        $this->display('setting/checkVersion');

    }

    public function doUpgrade(){
        $update=new \Addons\admin\model\UpdateModel;
        $version=$update->getVersion();
        $update=$update->actionUpgrade($version['version']['serverListApi'],$version['version']);
        show_json($update);
    }


    /**
     * 网站设置
     */
    public function configure(){
        global $_G;
        $calfbbs=\framework\library\Conf::all('calfbbs');
        $route=\framework\library\Conf::all('route');
        $email=\framework\library\Conf::all('email');
        $sms=\framework\library\Conf::all('sms');
        $file=new File();
        $tplList=$file->file_lists(CALFBB."/".$route['TPL']."/".$route['DEFAULT_MODULE'],0,'',1);
        $this->assign('tplList',$tplList);
        $this->assign('calfbbs',$calfbbs);
        $this->assign('route',$route);
        $this->assign('email',$email);
        $this->assign('sms',$sms);
        $this->display('setting/configure');
    }


    /**
     * 基础设置
     */
    public function saveConfigure(){
        global $_G;
        $file=new File();
        $text=CALFBB."/data/calfbbs.php";
        if(!is_writable($text) && is_file($text)){
            $this->error(url('admin/setting/configure',"t=configure"),'data下的calfbbs.php文件没有可写权限');
        }

        $saveText="<?php return ".var_export($_POST,true).";";
        $saveText=$file->file_write($text,$saveText,0777);

        if($saveText){
            $this->success(url('admin/setting/configure',"t=configure"));
        }else{
            $this->error(url('admin/setting/configure',"t=configure"));
        }

    }

    /**
     * 系统设置
     */
    public function saveRoute(){
        global $_G;
        $text=CALFBB."/data/route.php";
        if(!is_writable($text) && is_file($text)){
            $this->error(url('admin/setting/configure',"t=route"),'data下的route.php文件没有可写权限');
        }

        $route=$this->settingRoute();
        $file=new File();
        $saveText="<?php return ".var_export($route,true).";";
        $saveText=$file->file_write($text,$saveText,0777);
        if($saveText){
            $this->success(url('admin/setting/configure',"t=route"));
        }else{
            $this->error(url('admin/setting/configure',"t=route"));
        }

    }
    /**
     * 邮件设置
     */
    public function saveEmail(){
        global $_G;
        $file=new File();
        $text=CALFBB."/data/email.php";
        if(!is_writable($text) && is_file($text)){
            $this->error(url('admin/setting/configure',"t=configure"),'data下的email.php文件没有可写权限');
        }

        $saveText="<?php return ".var_export($_POST,true).";";
        $saveText=$file->file_write($text,$saveText,0777);

        if($saveText){
            $this->success(url('admin/setting/configure',"t=email"));
        }else{
            $this->error(url('admin/setting/configure',"t=email"));
        }

    }
    /**
     * 注册设置
     */
    public function saveSms(){
        global $_G;
        $file=new File();
        $text=CALFBB."/data/sms.php";
        if(!is_writable($text) && is_file($text)){
            $this->error(url('admin/setting/configure',"t=configure"),'data下的sms.php文件没有可写权限');
        }

        $saveText="<?php return ".var_export($_POST,true).";";
        $saveText=$file->file_write($text,$saveText,0777);

        if($saveText){
            $this->success(url('admin/setting/configure',"t=sms"));
        }else{
            $this->error(url('admin/setting/configure',"t=sms"));
        }

    }

    /** 拼接路由
     * @return  $route
     */
    public function settingRoute(){

        /**
         * 读取路由配置
         */
        $route=\framework\library\Conf::all('route');

        /**
         * 判断是否开启模版机制
         */

        if(@trim($_POST['TPL_STATUS'])=="on"){
            $route['TPL_APP']['TPL_STATUS']=true;
        }else{
            $route['TPL_APP']['TPL_STATUS']=false;
        }

        /**
         * 判断是否配置新模版
         */
        if(!empty($_POST['TPL_DEFAULT']) && $route['TPL_APP']['TPL_STATUS']==true){
            $route['TPL_APP']['TPL_DEFAULT']=$_POST['TPL_DEFAULT'];
        }

        /**
         * 判断是否开启伪静态后缀
         */
        if(@trim($_POST['SUFFIX_STATUS'])=="on"){
            $route['SUFFIX_STATUS']=true;
        }else{
            $route['SUFFIX_STATUS']=false;
        }


        /**
         * 判断是否设置伪静态后缀
         */
        if(!empty($_POST['SUFFIX']) && $route['SUFFIX_STATUS']==true){
            $route['SUFFIX']=$_POST['SUFFIX'];
        }

        /**
         * 判断是否隐藏url中的index.php
         */

        if(@trim($_POST['IDENX_SUFFIX'])=="on"){
            $route['IDENX_SUFFIX']=true;
        }else{
            $route['IDENX_SUFFIX']=false;
        }

        /**
         * 判断是否更改路由
         */
        if(!empty($_POST['PATH_INFO'])){
            $route['PATH_INFO']=$_POST['PATH_INFO'];
        }
        return $route;
    }

    /**
     * 密码修改
     */

    public function password(){

        if(http_method() == 'GET')
        {
            return $this->display('setting/password');
        }

        $response = $this->post(url("api/user/modifyPassword"), $_POST);
        if($response->code == 1001)
        {
            $access_token = self::$session->get('access_token');
            self::$session->del($access_token);
        }

        echo json_encode($response);
    }

}