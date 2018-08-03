<?php
/**
 * @className：帖子控制器
 * @description：获取帖子列表
 * @author:calfbb技术团队
 * Date: 2017/12/26
 */

namespace App\model;
use App\model\ApiModel;
class PostModel extends  ApiModel
{
    /**
     * 获取帖子列表
     */
    public function getPostList($cid=200000,$orderBy='create_time',$status=4,$page_size=10,$current_page=1,$keyword="",$uid="")//status默认除去删除状态的都选择
    {
        global $_G;

        $where['page_size']=$page_size;
        $where['current_page']=$current_page;
        if($status!=4){
            $where['status']=$status;
        }
        if($cid!=200000){
            $where['cid']=$cid;
        }
        if(!empty($keyword)){
            $where['title']=urldecode($keyword);
        }

        if(!empty($uid)){
            $where['uid']=$uid;
        }
        $where['orderBy']=$orderBy;
        $where['sort']="DESC";

        $data=$this->get(url("api/post/getPostList"),$where);

        if($data->code==1001 && $data->data){
            return  $data->data;
        }else{
            $data->data->list="";
            $data->data->pagination="";
            return  $data->data;
        }

    }
    public function getTopPosts($num=10,$top=1)
    {
        global $_G;

        $where['num']=$num;
        $where['top']=$top;
        $data=$this->get(url("api/post/getTopPosts"),$where);

        if($data->code==1001 && $data->data){
            return  $data->data;
        }else{
            return [];
        }
    }
    public function getHotPosts($num=10,$orderBy='reply_count',$sort='DESC')
    {
        global $_G;
        
        $where['num']=$num;
        $where['orderBy']=$orderBy;
        $where['sort']=$sort;
        $data=$this->get(url("api/post/getTimeMax"),$where);        
        if($data->code==1001 && $data->data){
            return  $data->data;
        }else{
            return [];
        }
    }

	//获取帖子内容
	public function getPostOne($post_id)
	{
		global $_G;
        $where['id']=$post_id;
        $data=$this->get(url("api/post/getPostOne"),$where);
        
        if($data->code==1001 && $data->data){
            return  (array)$data->data;
        }
        return [];
	}

    /**
     * 更新访问量点赞数 或回帖数
     */
    public function getChangeVisitRelies($data){
        $result=$this->get(url('api/post/changeVisitRelies'),$data);
        return $result;
    }

    public function getUserPostNumMethod($uid)
    {
        $where['uid']=$uid;
        $result=$this->get(url('api/post/getUserPostNum'),$where);
        if($result->code==1001 && $result->data){
             return (array)$result->data[0];
        }else{
            return [];
        }

    }



}