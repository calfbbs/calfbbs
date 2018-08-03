<?php
/**
 * @className：消息接口数据模型
 * @description：删除消息，更新为已读，查询消息
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\model;

use Addons\api\model\BaseModel;
class MessageModel  extends BaseModel
{
    /**
     * @var string $tableName 消息表名
     */
    private static  $tableName="massage";


    /** 修改一条消息为已读
     * @return int | bool $result
     */
    public function updateMessage($where){
        $result=db_update(self::$tableName,['is_read'=>1],$where);
        return $result;
    }

    /**
     * 添加一条消息
     * */
    public function addMessage(array $params = []){
        if(empty($params)){
            return false;
        }
        return db_insert(self::$tableName, $params);
    }

    /** 删除一条消息数据
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function deleteMessage($where){
        $result=db_delete(self::$tableName,$where);
        return $result;
    }

    /** 获取消息数据
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function selectMessage($data){

        $where = [];

        $where['puid']=$data['puid'];

        /*
         * 判断是否已读 1代表已读，0代表未读
         * */
        if(!empty($data['is_read'])){
            $where['is_read'] = $data['is_read'] ;
        }
      /*
       * 三张表的信息都在需要打开注释返回即可
       *   $sql = "SELECT massage.*,user.*,posts.* FROM `calf_massage` as massage LEFT JOIN `calf_user` user ON user.id=massage.uid LEFT JOIN `calf_posts` posts ON massage.posts_id=posts.id;";
       * $result = db_query($sql);
       *
       * */
      //var_dump($data['current_page']);die;
        $result=db_select(self::$tableName,$fields = "*",$where,$data['current_page'],$data['page_size'],$orderby=['id'=>'DESC']);
        return $result;
    }
    /**
     * 获取一条
     * */
    public function getOne($where){
        $res = db_select(self::$tableName,$fields = "*",$where);
        return $res;
    }

    /** 统计消息数据总条数
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function countMessage($data){

        $where = [];
        if(!empty($data['puid'])){
            $where['puid']=$data['puid'];
        }
        $result=db_count(self::$tableName,$where);
        return $result;
    }
}