<?php
/**
 * @className：首页控制器
 * @description：首页入口，加载首页模版,广告列表,友情链接
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace App\controller;
use App\controller\Base;
use App\model\ApiModel;
use App\model\AdvertisementModel;
use App\model\PostModel;

use App\model\NavModel;
use  \Gregwar\Captcha\CaptchaBuilder;
use  \Gregwar\Captcha\PhraseBuilder;
use  Framework\library\Session;

class Index extends Base
{
    const RESPONSE_SUCCESS = 1001;//请求成功
    const RESPONSE_FAILURE = 2001;
    public function __construct()
    {
        parent::__construct();
        /**
         * 获取分类列表
         */
        $classifyList=$this->column();
        $this->assign('classifyList',$classifyList);

    }

    //控制台
    public function index()
    {
        global $_G;

        $advertisement=new AdvertisementModel();
        $post=new PostModel();

        /**
         * 获取友情链接列表
         */
        $friendLinkList=$advertisement->getAdvertisementList(4);

        /**
         * 获取首页广告列表
         */
        $advertisementFristList=$advertisement->getAdvertisementList(1);
        /**
         * 获取首页置顶帖子列表
         */
        $topPostList=$post->getTopPosts();

        /**
         * 获取首页综合帖子列表
         */
        $comprehensivePostList=$post->getPostList();

        /**
         * 获取本周热议帖子列表
         */
        $hotPostList=$post->getHotPosts();


        $this->assign('link',$friendLinkList);
        $this->assign('firstList',$advertisementFristList);
        $this->assign('topPostList',$topPostList);
        $this->assign('comprehensivePostList',$comprehensivePostList->list);   
        $this->assign('hotPostList',$hotPostList);  

        $this->assign('cid',200000);
        $this->assign('status',4);
        $this->assign('orderBy','create_time');
        $this->display('index/index');
    }


    public function test(){
        $this->display('index/test');
    }

    public function search()
    {
        global $_G;

        $this->display('index/search');

    }



    public function cases()
    {
        global $_G;

        $this->display('index/cases');
    }
    public function tips()
    {
        global $_G;

        $this->display('index/tips');
    }
    public function notice()
    {
        global $_G;

        $this->display('index/notice');
    }
    public function e404()
    {
        global $_G;

        $this->display('index/404');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function captcha()
    {
        //生成验证码图片的Builder对象，配置相应属性
        $phraseBuilder = new PhraseBuilder(5, '0123456789');

        $builder = new CaptchaBuilder(null,$phraseBuilder);

        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 45, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
        $session=new Session();
        //把内容存入session
        $session->set('postcaptcha', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }

    /**
     * @function 富文本编辑器图片 上传
     */
    public function doUploadPic()
    {
        global $_G;

        $res = $this->post(url("api/files/uploadFile"), ['file' => $_FILES['wangEditorH5File']]);
        echo ($res->code == self::RESPONSE_SUCCESS ? $_G['ATTACHMENT_ROOT'] . '/' : 'error|') . $res->data;
    }

}