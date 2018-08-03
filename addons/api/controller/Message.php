<?php

/**
 * @className：消息相关接口管理
 * @description：删除消息，更新为已读，查询消息
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\controller;

use Addons\api\model\MessageModel;
use Addons\api\model\PostModel;
use Addons\api\model\UserModel;
use Addons\api\validate\MessageValidate;

class Message extends MessageModel
{
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    /**
     * 更新消息为已读
     * @param id $id 更新id
     *
     */
    public function changeMessage()
    {
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new MessageValidate();
        $validateResult = $validate->delMessageValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        /**
         * 更新到数据到数据库
         */
        $result = $this->updateMessage(['id' => $validateResult['id']]);


        if ($result) {
            return $this->returnMessage(1001,'响应成功',$result);
        } else {
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 添加消息
     * @param uid 通知人id
     * @param puid 被通知人id
     * @param posts_id 通知帖子id
     * */
    public function addMassage(){
        $validate = new MessageValidate();
        $validateResult = $validate->addMassageValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $posts = (new PostModel())->findPost($validateResult['posts_id']);
        if(!$posts){
            return $this->returnMessage(2001,'通知帖子错误',[]);
        }
        $data['posts_title'] = $posts['title'];
        $data['posts_id'] = $posts['id'];

        if($validateResult['puid'] !=-1){
            $puid = (new UserModel())->getUser(['uid'=>$validateResult['puid']]);
            if(!$puid){
                return $this->returnMessage(2001,'被通知人参数错误',[]);
            }
            $data['puid'] = $validateResult['puid'];
        }else{
            $data['puid']=$posts['uid'];
        }

        //获取通知人昵称
        $data['username'] = (new UserModel())->getUser(['uid'=>$validateResult['uid']],'username');
        if(!$data['username']){
            return $this->returnMessage(2001,'通知人错误',[]);
        }
        $data['uid'] = $validateResult['uid'];



        $data['add_time'] = time();
        $data['is_read'] = 2 ;
        $result=[];
        if($data['puid'] != $data['uid']){
            $result = $this->addMessage($data);
        }else{
            return $this->returnMessage(2001,'回帖通知人跟接收消息人不能一致',$result);
        }

        if ($result) {
            return $this->returnMessage(1001,'响应成功',$result);
        } else {
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 删除消息
     * @param id $id 要删除的消息id
     */
    public function delMessage()
    {
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new MessageValidate();
        $validateResult = $validate->delMessageValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        /**
         * 获取一条数据
         * */

        $res = $this->getOne(['id' => $validateResult['id']]);
        if (!$res) {
            return $this->returnMessage(2001,'消息不存在',[]);
        }

        /**
         * 删除数据库数据
         */
        $result = $this->deleteMessage(['id' => $validateResult['id'],'puid'=>$validateResult['puid']]);


        if ($result) {
            return $this->returnMessage(1001,'响应成功',$result);
        } else {
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 清空消息
     * */
    public function emptyMessage(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new MessageValidate();
        $validateResult = $validate->emptyMassageValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $result = $this->deleteMessage(['puid'=>$validateResult['puid']]);

        if ($result) {
            return $this->returnMessage(1001,'响应成功',$result);
        } else {
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }


    /**
     * 获取消息列表
     * @param int $uid 用户id
     * @param int $current_page 当前页
     * @param int $page_size 每页显示数量
     * @param int $is_read 是否已读
     */
    public function getMessageList()
    {
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new MessageValidate();
        $validateResult = $validate->getMessageListValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $validateResult['page_size'] = !empty($validateResult['page_size']) ? $validateResult['page_size'] : 10;
        $validateResult['current_page'] = !empty($validateResult['current_page']) ? $validateResult['current_page'] : 1;

        /**
         * 获取当前页数总条数
         */
        $count = $this->countMessage($validateResult);
        if ($count > 0) {
            /**
             * 查询广告列表
             */
            $result = $this->selectMessage($validateResult);

        } else {
            $result = null;
        }

        $data['pagination'] = $this->getPagination($validateResult['page_size'], $validateResult['current_page'], $count);

        if ($result) {
            $data['list'] = $result;
            return $this->returnMessage(1001,'响应成功',$data);
        } else {
            $data['list'] = [];
            return $this->returnMessage(2001,'响应错误',$data);
        }
    }




}