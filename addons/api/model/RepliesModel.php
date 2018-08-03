<?php
/**
 * @className：帖子回复接口数据库模型
 * @description：帖子回复的增删改查 排序 分页 点赞。。。
 * @author:calfbbs技术团队
 * Date: 2017/11/16
 * Time: 下午9:26
 */
namespace Addons\api\model;
use Addons\api\model\BaseModel;

class RepliesModel extends BaseModel
{
    /**
     * @var string $tableName 帖子回复表
     */
    private static  $tableName="replies";
    /**
     * @var string $postsName 帖子表
     */
    private static  $postsName = "posts";
    /**
     * @var string $usersTableName 用户表
     */
    private static  $usersTableName="user";
    /**
     * @var string $usersTableName 点赞关系表
     */
    private static  $praiseRecordTableName="praise_record";

    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    /** 显示帖子下的所有评论
     * @param array $data 传入数据
     * @param array $U  检测用户是否登陆
     * @return int | bool $result
     */
    public function showRepliesModel($data)
    {
        $where = [];

        $where['reid'] = $data['reid'];


        $true = db_find(self::$tableName,$fields="*",$where);
        if(!$true){
            return $this->returnMessage(2001,'数据库中不存在此reid',0);
        }

        $data['current_page'] = (isset($data['current_page'])) ? $data['current_page'] : 1;

        $data['page_size'] = (isset($data['page_size'])) ? $data['page_size'] : 10;
        $tableName=table_prefix(self::$tableName);
        $usersTableName=table_prefix(self::$usersTableName);
        $limit = ($data['current_page'] - 1) * $data['page_size'] ;
        $sql="
select 
a.*,b.username,b.status,b.avatar
from {$tableName} as a,{$usersTableName} as b   where reid={$where['reid']} and a.uid=b.uid order by create_time asc  limit " . $limit . "," . $data['page_size'];

        $result=db_query($sql);
        return $result;
    }

    /**  获取帖子回复的总条数
     * @param  $reid  帖子ID
     * @return int | bool $result
     */
    public function countReplies($reid)
    {
        $where['reid'] = $reid;
        $result=db_count(self::$tableName,$where);
        return $result;
    }
    /**  发布帖子信息
     * @param array $data 传入数据
     * @return int | bool $result
     */
    public function postReplies($data)
    {
        $result= db_insert(self::$tableName,$data);
        $result = db_id();
      //  $res = db_query("SELECT a.uid,a.username,b.create_time,b.reply_text,b.thumb_cnt,b.id,b.puid FROM calf_user AS a,calf_replies AS b WHERE  b.id=".$result." AND a.uid=b.uid LIMIT  0,1");
        return $result;
    }

    /** 获取评论数据最大ID
     * @return int | id
     */
    public function MaxId()
    {
        $result = db_fetchall("SELECT id FROM calf_replies");
        return  max($result);
    }
    /**  评论帖子点赞
     * @param array $data 传入数据
     * @return int | bool $result
     */
    public function thumbReplies($data)
    {
        $result=db_find(self::$praiseRecordTableName,'*',$data);

        if(!$result){
            $result=db_insert(self::$praiseRecordTableName,$data);
        }else if(@$result['status']==0){
            $where=$data;
            $data['status']=1;
            $result=db_update(self::$praiseRecordTableName,$data,$where);
        }else{
            return false;
        }


        if($result){
            $postTable=table_prefix(self::$tableName);
          //  echo "update {$postTable}  set  thumb_cnt=thumb_cnt+1  where  id ={$data['rid']} ";
           return  db_query("update {$postTable}  set  thumb_cnt=thumb_cnt+1  where  id ={$data['rid']} ");
        }else{
            return false;
        }

    }
    /** 评论帖子取消赞
     * @param array $data 传入数据
     * @return int | bool $result
     */
    public function offthumbReplies($data)
    {
        $result=db_find(self::$praiseRecordTableName,'*',$data);

        if(!$result){
            return true;
        }else if(@$result['status']==1){
            $where=$data;
            $data['status']=0;
            $result=db_update(self::$praiseRecordTableName,$data,$where);
        }else{
            return false;
        }

        if($result){
            $postTable=table_prefix(self::$tableName);
            //  echo "update {$postTable}  set  thumb_cnt=thumb_cnt+1  where  id ={$data['rid']} ";
            return  db_query("update {$postTable}  set  thumb_cnt=thumb_cnt-1  where  id ={$data['rid']} ");
        }else{
            return false;
        }
    }
    /** 删除帖子回复内容
     * @param array $where 条件
     * @return int | bool $result
     */
    public function deleteReplies($where)
    {
        $res = db_delete(self::$tableName,array('id'=>$where['id']));
        return $res;
    }
    /** 编辑帖子回复内容
     * @param array $data 传入数据
     * @return int | bool $result
     */
    public function updateReplies($data)
    {
        $where['id'] = $data['id'];
        $res = db_update(self::$tableName,$data,array('id'=>$data['id']));
        return $res;
    }
    /** 显示帖子下用户回复的多条记录
     * @param array $where 条件
     * @return int | bool $result
     */
    public function selectReplies($where)
    {
        $result=db_select(self::$tableName,$fields = "*",$where);
        return $result;
    }
    /** 验证数据是否存在
     * @param array $data 数据条件
     * @return int | bool $result
     */
    public function uniqueMethod($data)
    {
        $where['id'] = $data;
        $true = db_find(self::$postsName,$fields="*",$where);
        return  $true;
    }

    /** 验证数据是否存在
     * @param array $data 数据条件
     * @return int | bool $result
     */
    public function getPraiseRecordReplies($where)
    {

        $result= db_find(self::$praiseRecordTableName,$fields="*",$where);
        return  $result;
    }
}