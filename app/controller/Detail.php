<?php
/**
 * @className：首页控制器
 * @description：首页入口，加载首页模版,广告列表,友情链接
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */

namespace App\controller;
use App\controller\Base;
use App\model\AdvertisementModel;
use App\model\PostModel;
use App\model\UserModel;
use App\model\RepliesModel;
use App\model\MessageModel;
use  Framework\library\Session;
class Detail  extends Base
{
    public function index(){
        $advertisement = new AdvertisementModel();
        /**
         * 获取详情页广告列表
         */
        $advertisementDetailList = $advertisement->getAdvertisementList(3);
        
        $post = new PostModel();
        $user = new UserModel();

        $replies = new RepliesModel();
        /**
         * 获取帖子内容
         */
        if(empty($_GET['id'])){
            header("Location:".url('app/index/e404'));
            return;
        }
        $post_id=$_GET['id'];
        $posts = $post->getPostOne($post_id);

        $title = @$posts['title'] ?  @$posts['title'] :"calfbbs 经典开源社区系统,bbs论坛";
       // $keywords = @$posts['description'] ?  @$posts['description']:"calfbbs 经典开源社区系统,bbs论坛";
        $keywords=$title;
        $description = @$posts['text'] ?  @strip_tags($posts['text']) :"calfbbs 经典开源社区系统,bbs论坛";

        //获取用户信息
        $userInfo = $user->getUserOne($posts['uid']);
        //获得访问用户信息
        $access_token=self::$session->get('access_token');
        $loginUserinfo=self::$session->get($access_token);
        /**
         * 获取分类列表
         */
        $classifyList=$this->column();



        /**
         * 获取帖子回复列表
         */
        @$current_page=!empty($_GET['current_page']) ? $_GET['current_page'] : 1;
        @$page_size=!empty($_GET['page_size']) ? $_GET['page_size'] : 10;
        $replieList=$replies->getRepliesAll($post_id,$page_size,$current_page);


        /**
         * 获取本周热议帖子列表
         */
        $hotPostList=$post->getHotPosts();
        $this->assign('classifyList',$classifyList);
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->assign('advertisementlList',$advertisementDetailList);
        $this->assign('posts',$posts);
        $this->assign('userInfo',$userInfo);
        $this->assign('loginUserinfo',$loginUserinfo);
        $this->assign('replieList',$replieList);
        $this->assign('hotPostList',$hotPostList);
        $this->assign('post_id',$post_id);
        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->display('detail/index');
    }

    /**
     * ajax 提交回复
     */
    public function  commit(){

        $replies = new RepliesModel();
        $result=$replies->postInsRsplies($_POST);
        if($result->code==1001 && $result->data){
                $messageData['puid']=$_POST['puid'];
                $messageData['uid']=$_POST['uid'];
                $messageData['posts_id']=$_POST['reid'];
                $message = new MessageModel();
                $message->addMessage($messageData);
            $post = new PostModel();
            $post->getChangeVisitRelies(['id'=>$_POST['reid'],'type'=>2,'action'=>'add']);
        }
        show_json($result);

    }

    /**
     * ajax 点赞
     */
    public function insThumbRepies(){
        $replies = new RepliesModel();
        $result=$replies->postInsThumbRepies($_GET);
        show_json($result);
    }

    /**
     * ajax 取消点赞
     */
    public function cancelthumbReplies(){
        $replies = new RepliesModel();
        $result=$replies->postCancelthumbReplies($_GET);
        show_json($result);
    }
    /**
     * ajax 查看回帖是否点过赞
     */
    public function getPraiseRecord(){
        $replies = new RepliesModel();
        $result=$replies->getPraiseRecord($_GET);
        show_json($result);
    }

    /**
     * 更新访问量
     */
    public function changeVisitRelies(){
        $post = new PostModel();
        $result=$post->getChangeVisitRelies($_GET);
        show_json($result);
    }

    /**
     * 删除回帖
     */
    public function delReplies(){
        $replies = new RepliesModel();
        $result=$replies->getdelReplies($_GET);
        if($result->code==1001 && $result->data){
            $post = new PostModel();
            $data=$post->getChangeVisitRelies(['id'=>$_GET['reid'],'type'=>2,'action'=>'minus']);

        }
        show_json($result);
    }
}