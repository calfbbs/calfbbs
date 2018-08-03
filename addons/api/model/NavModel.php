<?php
/**
 * @className：广告接口数据模型
 * @description：增加广告，删除广告，编辑广告，查询广告
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\model;

use Addons\api\model\BaseModel;
class NavModel  extends  BaseModel
{
    /**
     * @var string $tableName 广告表名
     */
    private static  $tableName="nav";

    /** 插入一条数据到广告
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function insertNav($data){
        $result=db_insert(self::$tableName,$data);
        return $result;
    }

    /** 插入一条数据到广告
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function updateNav($data,$where){
        $result=db_update(self::$tableName,$data,$where);
        return $result;
    }

    /** 删除一条广告数据
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function deleteNav($where){
        $result=db_delete(self::$tableName,$where);
        return $result;
    }

    /** 获取广告数据列表
     * @param $data 传入数据
     * @return array | bool $result
     */
    public function selectNav($data){

        $where = [];
       /* if(!empty($data['cid'])){
            $where['cid']=$data['cid'];
        }*/
        $result=db_select(self::$tableName,$fields = "*",$where,$data['current_page'],$data['page_size'],$orderby=['sort'=>'DESC']);
        return $result;
    }

    /** 统计广告数据总条数
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function countNav($data){

        $where = [];
       /* if(!empty($data['cid'])){
            $where['cid']=$data['cid'];
        }*/
        $result=db_count(self::$tableName,$where);
        return $result;
    }


    public function findNav($data,$fields = '*'){
        $where = [];

        if(!empty($data['id'])){
            $where['id']=$data['id'];
        }
        $result=db_find(self::$tableName,$fields ,$where);
        
        return $result;
    }
}