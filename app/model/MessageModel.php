<?php
/**
 * @className：帖子控制器
 * @description：获取帖子列表
 * @author:calfbb技术团队
 * Date: 2017/12/26
 */

namespace App\model;
use App\model\ApiModel;
class MessageModel extends  ApiModel
{
    /**
     * 添加通知消息
     */
    public function addMessage($data){
        $result=$this->get(url('api/Message/addMassage'),$data);
        return $result;
    }



}