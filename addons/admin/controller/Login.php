<?php
/**
 * @className：插件应用路由文件
 * @description：首页入口，文章页入口，公告页入口，用户中心入口
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace Addons\admin\controller;
use Addons\admin\controller\Base;
use  Framework\library\Session;
class Login  extends Base
{



    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 登陆入口
     * @return string
     */
    public function index(){
        global $_G,$_GPC;

        $this->display('login/login');
    }

    /**
     * 登陆
     */
    public function login(){
        $data=$this->post(url("api/user/login"),$_POST);

        if($data->code==1001){
            /**
             * 验证是否管理员
             */
            if($data->data->status !=2){
                $data->data="当前账号不是管理员账号,无法登陆";
                $data->code=2001;
                $data->message='响应错误';
            }else{
                /**
                 * access_token处理
                 */
                $access_token=md5($this->randomkeys(6)+$data->data->uid);
                $session=new Session();
                $access_token=$session->set('access_token',$access_token);
                $userinfo=$session->set($access_token,(array)$data->data);
                $data->data="登陆成功";
            }

        }
        echo json_encode($data);

    }
    /**
     * 退出
     * @return string
     */
    public function loginOut(){
        $access_token=self::$session->get('access_token');
        self::$session->del($access_token);
        $this->success(url("admin/login/index"),'退出成功');

    }


    /**
     * 生成6位字母+数字随机数
     * @param $length
     * @return null|string
     */
    private function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = null;
        for($i=0; $i<$length; $i++)
        {
            $key .= $pattern{mt_rand(0,35)};    //生成php随机数
        }
        return $key;
    }


}