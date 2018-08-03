<?php
/**
 * @className：search控制器
 * @description：搜索页面
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace App\controller;
use App\controller\Base;
use App\model\AdvertisementModel;
use App\model\PostModel;
class Search extends Base
{

    //控制台
    public function search()
    {

        global $_G;
        $title="calfbbs 经典开源社区系统,bbs论坛";
        $keywords="calfbbs 经典开源社区系统,bbs论坛";
        $description="calfbbs 经典开源社区系统,bbs论坛";
        @$status=isset($_GET['status']) ? $_GET['status'] : 4;
        @$orderBy=!empty($_GET['orderBy']) ? $_GET['orderBy'] : 'create_time';
        @$cid=!empty($_GET['cid']) ? $_GET['cid'] : 200000;
        @$current_page=!empty($_GET['current_page']) ? $_GET['current_page'] : 1;
        @$page_size=!empty($_GET['page_size']) ? $_GET['page_size'] : 10;
        @$keyword=!empty($_GET['keyword']) ? $_GET['keyword'] : "";
        $advertisement=new AdvertisementModel();
        $post=new PostModel();


        /**
         * 获取友情链接列表
         */
        $friendLinkList=$advertisement->getAdvertisementList(4);
        /**
         * 获取分类页广告列表
         */
        $advertisementFristList=$advertisement->getAdvertisementList(2);
        /**
         * 获取分类帖子列表
         */
        $topPostList=$post->getPostList($cid,$orderBy,$status,$page_size,$current_page,$keyword);
        /**
         * 获取本周热议帖子列表
         */
        $hotPostList=$post->getHotPosts();
        
        /**
         * 获取分类列表
         */
        $classifyList=$this->column();






        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->assign('link',$friendLinkList);
        $this->assign('firstList',$advertisementFristList);        
        $this->assign('topPostList',$topPostList->list);
        $this->assign('pagination',$topPostList->pagination);
        $this->assign('hotPostList',$hotPostList);    
        $this->assign('classifyList',$classifyList);    
        $this->assign('status',$status);
        $this->assign('orderBy',$orderBy); 
        $this->assign('cid',$cid);
        $this->display('search/search');
    }
    public function column(){
        global $_G;
        $data=$this->get(url("api/classify/getClassifylist"));
        if($data->code==1001 && $data->data){
            return  $data->data;
        }else{
            return [];
        }
    }
    

}