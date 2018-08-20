<?php
/**
 * @className：示例控制器
 * @description：首页入口，加载首页模版
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace App\controller;
use App\controller\Base;
use  Framework\library\View;
use App\model\UserModel;
use App\model\PostModel;
use App\model\RegisterModel;
class Users extends Base
{

    public function __construct()
    {
        parent::__construct();
        $this->validateLogin();//验证是否登陆
    }

    public function index()
    {
        global $_G;
        $post=new PostModel();
        @$current_page=!empty($_GET['current_page']) ? $_GET['current_page'] : 1;
        @$page_size=!empty($_GET['page_size']) ? $_GET['page_size'] : 10;
        $getPostList=$post->getPostList($cid=200000,$orderBy='create_time',$status=4,$page_size,$current_page,$keyword="",$uid=self::$userinfo['uid']);
        $userPostNum = $post->getUserPostNumMethod(self::$userinfo['uid']);
//        echo "<pre>";
//        var_dump($getPostList);die;
        $this->assign('userPostNum',$userPostNum);
        $this->assign('postList',$getPostList);
        $this->assign('pagination',@$getPostList->pagination);
        $this->display('users/index');

    }

    /**
     * 个人中心
     */
    public function home()
    {
        global $_GPC;
        if(@empty($_GPC['uid'])){
            header("Location:".url('app/index/e404'));
            return;
        }
        $user = new UserModel();
        $userinfo=$user->getUserOne($_GPC['uid']);
        $getAnswers=$user->getAnswers($_GPC['uid']);
        $getQuestions=$user->getQuestions($_GPC['uid']);
        $this->assign('answersList',$getAnswers);
        $this->assign('questionsList',$getQuestions);
        $this->assign('user',$userinfo);
        $this->display('users/home');

    }

    /**
     * 个人设置
     */
    public function set()
    {
        global $_GPC;
        $param['dir_name']='login';
        $data=$this->post(url("api/modules/getModules"),$param);
        if($data->code==1001){//判断login是否安装
            $modules=$data->data->modules;
            $register=new RegisterModel();
            $registerinfo=$register->getRegisterOne(['type'=>'uid','uid'=>$_GPC['uid']]);
            $this->assign('registerinfo',$registerinfo);
        }

        $user = new UserModel();
        $userinfo=$user->getUserOne($_GPC['uid']);
        if(($userinfo['email']=='')&&($userinfo['mobile']=='')){
            $userinfo['change_username']=0;
        }else{
            $userinfo['change_username']=1;
        }        
        $this->assign('user',$userinfo);
        $this->display('users/set');

    }

    /**
     * 消息中心
     */
    public function message()
    {
        global $_G;
        //获取参数
        @$param['current_page']=$_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size']=$_GET['page_size'] ? $_GET['page_size'] : 10;
        @$param['is_read'] = isset($_GET['is_read']) ? $_GET['is_read'] : 0 ;
        @$param['puid'] = self::$userinfo['uid'];
        if(!$param['puid']){
            header("Location:".url('app/index/e404'));
            return;
        }
        $data=$this->get(url("api/message/getMessageList"),$param);

        $list="";
        $pagination="";
        if($data->code==1001 && $data->data){
            $list=$data->data->list;
            $pagination=$data->data->pagination;
        }
        $this->assign('pagination',$pagination);
        $this->assign('list',$list);
        $this->assign('is_sel',$param['is_read']);
        $this->display('users/message');
    }


    /**
     * 删除一条消息
     * */
    public function delMessage(){

        $data=$this->get(url("api/message/delMessage",['id'=>$_GET['id'],'puid'=>$_GET['puid']]));
        show_json($data);

    }

    /**
     * 清空消息
     * */
    public function emptyMessage(){

            $data=$this->get(url("api/message/emptyMessage",['puid'=>$_GET['puid']]));
            show_json($data);
    }


    public function activate()
    {
        global $_G;
        
        $this->display('users/activate');

    }

    /**
     * 更新用户session缓存信息
     */

    public function updateUserInfo($uid){
        $userInfo=$this->get(url("api/user/getUserInfo",['uid'=>$uid]));
        if($userInfo->code==1001 && $userInfo->data){
            /**
             *  用户缓存数据更新
             */
            $access_token=self::$session->get('access_token');
            self::$userinfo=self::$session->set($access_token,(array)$userInfo->data);
        }
    }
    /**
     * 修改用户基础信息
     */
    public function saveUserBase(){

        $data=$this->post(url("api/user/updateUser",$_POST));
        $this->updateUserInfo($_POST['uid']);
        show_json($data);
    }

    /**
     * 修改用户头像
     */
    public function saveUserAvatar($data){
        $user=$this->post(url("api/user/updateUser"),$data);
        if($user->code==1001 && $user->data){
            $this->updateUserInfo($data['uid']);
            return true;
        }
        return false;
    }

    /**
     * 修改用户密码
     */
    public function saveUserPassword(){
        $data=$this->post(url("api/user/modifypassword",$_POST));
        show_json($data);
    }

    /**
     * @function 图片 上传
     */
    public function doUploadPic()
    {
        global $_GPC;

        $result = $this->post(url("api/files/uploadFile"), ['file' => $_FILES['file'],'width'=>168]);
        if($result->code==1001 && $result->data){
            $data=$this->saveUserAvatar(['uid'=>$_GPC['uid'],'avatar'=>$result->data]);
            if($data){
                show_json($result);
            }else{
                $result->code=2001;
                $result->message="响应失败";
                $result->data="请检查文件夹是否有写入权限";
                show_json($result);
            }
        }
        show_json($result);
    }
}
