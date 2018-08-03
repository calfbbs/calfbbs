<?php
/**
 * @className：帖子回复接口功能方法接口
 * @description：帖子回复点赞
 * @author:calfbbs技术团队
 * Date: 2017/11/16
 * Time: 下午9:23
 */
namespace Addons\api\controller;
use Addons\api\model\RepliesModel;
use Addons\api\validate\RepliesValidate;
use Addons\api\model\UserModel;
class  Replies extends RepliesModel
{

    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    public  function indexs()
    {
        global $_G,$_GPC;
        $res = $this->postReplies($this->post);
    }

    /*
     *   获取帖子的所有动态评论
     *
     * */

    public function showReplies()
    {
        $validate = new RepliesValidate();
        $validateResult = $validate->showRepliesValidate($this->get);

        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $numReidValue = $this->get['reid'];


        $countReplies = $this->countReplies($numReidValue);
        $result=[];
        if($countReplies > 0){
            /**
             * 查询回复列表
             */
            $result=$this->showRepliesModel($this->get);
            if(!empty($result)){
                $user=new UserModel();
                foreach($result as $key=>$value){
                   if($value['puid'] !=-1 && !empty($value['puid'])){
                       $username=$user->getUser(['uid'=>(int)$value['puid']],'username');
                       if($username){

                         $result[$key]['pusername']=$username;
                       }else{
                         $result[$key]['pusername']="";
                       }

                   }else{
                         $result[$key]['pusername']="";
                   }
                }
            }
        }


        $data['pagination']=$this->getPagination($validateResult['page_size'],$validateResult['current_page'],$countReplies);

        if($result){
            $data['list']=$result;
            return  $this->returnMessage(1001,'响应成功',$data);
        }else{
            $data['list']=[];
            return  $this->returnMessage(2001,'响应错误',$data);

        }
    }

    /**
     * 查看用户回复的回复数据
     * @param uid    回贴人id
     * @param puid   被回帖人id
     * @param reid   主贴id
     */
    public  function getUsersReplies()
    {

        /**
         *  参数数据校验
         *
         * */
        $validate = new RepliesValidate();

        $validateResult = $validate->getReplyRepliesListValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $result  = $this->selectReplies($validateResult);
        if($result)
        {
            return $this->returnMessage(1001,'响应成功',$result);
        }else{
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 删除回复内容
     * @param id
     */

    public function delReplies()
    {
        /*
         * 删除帖子回复 参数id校验
         *
         * */

        $validate = new RepliesValidate();
        $validateResult = $validate->delRepliesValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $result = $this->deleteReplies($validateResult);
        if ($result)
        {
            return $this->returnMessage(1001,'删除成功',true);
        }else{
            return $this->returnMessage(2001,'删除失败',false);
        }
    }

    /**
     * 发布回帖数据
     * @param  uid	int	回帖人的uid
     * @param  puid	int	被回帖人的uid
     * @param  reid  帖子主id
     * @param  reply_text	text	回帖内容
     * @param  create_time	int	回帖时间
     * @param  top	int	是否置顶 1为置顶 默认0
     * @param  thumb_cnt	int	点赞数   --------点赞功能 初始发布不存在有赞👍---数据调取即可
     */

    public function insRsplies()
    {

       //参数校验
        $validate=new RepliesValidate();
        $validateResult=$validate->addRepliesValidate($this->post);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        //回帖创建时间  - 未转换  - 有需求可随时可以转换
        $validateResult['create_time'] = time();
        //数据入库
        $result=$this->postReplies($validateResult);

        if($result)
        {
            return $this->returnMessage(1001,'回复成功',(int)$result);
        }else{
            return $this->returnMessage(2001,'回复失败',$result);
        }

    }

    /**
     *
     *   回复贴点赞方法
     *
     * */

    public function insthumbRepies()
    {
        //参数校验
        $validate=new RepliesValidate();
        $validateResult=$validate->insthumbRepiesValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }


        $result= $this->thumbReplies($validateResult);
        if($result)
        {
            return $this->returnMessage(1001,'点赞成功',true);
        }else{
            return $this->returnMessage(2001,'点赞失败',false);
        }


    }

    /**
     *
     * 用户取消点赞方法
     *
     * */

    public function cancelthumbReplies()
    {
        //参数校验
        $validate=new RepliesValidate();
        $validateResult=$validate->insthumbRepiesValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $result= $this->offthumbReplies($validateResult);
        if($result)
        {
            return $this->returnMessage(1001,'已取消点赞',true);
        }else{
            return $this->returnMessage(2001,'取消点赞失败',false);
        }

    }

    /*
     * 编辑帖子回复的内容
     *
     * */

    public function changeReplies()
    {

        $validate = new RepliesValidate();
        $validateResult = $validate->updateRepliestValidate($this->post);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        //回帖修改时间  - 未转换  - 有需求可随时可以转换
        $validateResult['change_time'] = time();
        //数据入库
        $result = $this->updateReplies($validateResult);

        if($result)
        {
            return $this->returnMessage(1001,'修改成功',true);
        }else{
            return $this->returnMessage(2001,'修改失败',false);
        }

    }


    /**
     * 查看用户是否给该评论回帖点过赞
     */
    public function getPraiseRecord(){
        $validate = new RepliesValidate();
        $validateResult = $validate->getPraiseRecordValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        //数据入库
        $result = $this->getPraiseRecordReplies($validateResult);

        if($result)
        {
            return $this->returnMessage(1001,'响应成功',$result);
        }else{
            return $this->returnMessage(2001,'响应失败',false);
        }
    }




}