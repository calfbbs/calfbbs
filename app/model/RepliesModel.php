<?php
/**
 * @author rock
 * Date: 2018/2/3 下午12:28
 */

namespace App\model;

use App\model\ApiModel;
class RepliesModel extends ApiModel
{
    /**
     * 获取帖子下面所有相关评论
     */
    public function getRepliesAll($reid,$page_size=20,$current_page=1){
        $where['reid']=$reid;
        $where['page_size']=$page_size;
        $where['current_page']=$current_page;
        $result=$this->get(url('api/replies/showReplies'),$where);

        if($result->code==1001 && $result->data){
            return $result->data;
        }else{
            return [];
        }
    }

    /**
     * 回复
     */
    public function  postInsRsplies($data){

        $result=$this->get(url('api/replies/insRsplies'),$data);
        return $result;
    }

    /**
     * 点赞
     */
    public function postInsThumbRepies($data){
        $result=$this->get(url('api/replies/insthumbRepies'),$data);
        return $result;
    }

    /**
     * 取消点赞
     */
    public function postCancelthumbReplies($data){
        $result=$this->get(url('api/replies/cancelthumbReplies'),$data);
        return $result;
    }

    /**
     * 查看回帖是否点过赞
     */
    public function getPraiseRecord($data){
        $result=$this->get(url('api/replies/getPraiseRecord'),$data);
        return $result;
    }

    /**
     * 删除回帖
     */
    public function getdelReplies($data){
        $result=$this->get(url('api/replies/delReplies'),$data);
        return $result;
    }

}