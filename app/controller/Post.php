<?php
/**
 * @author rock
 * Date: 2018/2/4 下午12:05
 */

namespace App\controller;
use App\controller\Base;
use App\model\ApiModel;
use  \Gregwar\Captcha\CaptchaBuilder;
use  \Gregwar\Captcha\PhraseBuilder;
use  Framework\library\Session;
use App\model\PostModel;
class Post  extends Base
{
    /**
     * 发布帖子
     */
    public function add()
    {
        global $_G;

        if ($_POST){

            if(self::$session->get('postcaptcha') !=$_POST['vercode']){
                show_json(['code'=>2001,'message'=>'响应错误','data'=>'验证码输入错误']);
            }
            if ( !empty($_POST['submit'])) {
                $_POST['uid'] = self::$userinfo['uid'];

                $data = $this->post(url("api/post/addPost"), $_POST);
                if($data->code==1001 && $data->data){
                    $data->data="发帖成功";
                    show_json((array)$data);
                }else{
                  //  $data->data="发帖失败";
                    show_json((array)$data);
                }
            }

        }

        $userinfo = self::$userinfo;
        if (empty($userinfo)) {
            $this->error(url('app/login/index'), '请先登录');
        }


        //验证用户是否存在，不存在 显示用户不能发帖
        $clssify=$this->get(url("api/classify/getClassify"));
        $this->assign('clssify', $clssify);

        $this->display('post/add');

    }

    /**
     * 修改帖子
     */
    public function edit()
    {
        global $_G;

        if ($_POST){

            if(self::$session->get('postcaptcha') !=$_POST['vercode']){
                show_json(['code'=>2001,'message'=>'响应错误','data'=>'验证码输入错误']);
            }
            if ( !empty($_POST['submit'])) {
                $_POST['uid'] = self::$userinfo['uid'];

                $data = $this->post(url("api/post/changePost"), $_POST);

                if($data->code==1001 && $data->data){
                    $data->data="编辑帖子成功";
                    show_json((array)$data);
                }else{
                   // $data->data="编辑帖子失败";
                    show_json((array)$data);
                }
            }

        }

        $userinfo = self::$userinfo;
        if (empty($userinfo)) {
            $this->error(url('app/login/index'), '请先登录');
        }

        if(empty($_GET['id'])){
            header("Location:".url('app/index/e404'));
            return false;
        }
        $post=$this->get(url("api/post/getPost"),$_GET);
        if($post->code==1001 && $post->data){
            $post=$post->data;
        }

        //验证用户是否存在，不存在 显示用户不能发帖
        $clssify=$this->get(url("api/classify/getClassify"));


        $this->assign('clssify', $clssify);
        $this->assign('post', $post);
        $this->display('post/edit');
    }
}