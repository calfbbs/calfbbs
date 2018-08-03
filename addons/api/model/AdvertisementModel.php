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

class AdvertisementModel extends BaseModel
{
    /**
     * @var string $tableName 广告表名
     */
    private static  $tableName="advertisement";

    /** 插入一条数据到广告
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function insertAdvertisement($data){
        $result=db_insert(self::$tableName,$data);
        return $result;
    }

    /** 更新广告数据
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function updateAdvertisement($data,$where){
        $result=db_update(self::$tableName,$data,$where);
        return $result;
    }

    /** 删除一条广告数据
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function deleteAdvertisement($where){
        $result=db_delete(self::$tableName,$where);
        return $result;
    }

    /** 获取广告数据列表
     * @param $data 传入数据
     * @return array | bool $result
     */
    public function selectAdvertisement($data){

        $where = [];
        if(!empty($data['cid'])){
            $where['cid']=$data['cid'];
        }

        $result=db_select(self::$tableName,$fields = "*",$where,$data['current_page'],$data['page_size'],$orderby=['id'=>'DESC','sort'=>'DESC']);
        return $result;
    }

    /** 统计广告数据总条数
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function countAdvertisement($data){

        $where = [];
        if(!empty($data['cid'])){
            $where['cid']=$data['cid'];
        }
        $result=db_count(self::$tableName,$where);
        return $result;
    }

    /**查找单条广告数据
     * @param  array      $data
     * @param string $fields
     *
     * @return \Ambigous|string
     */
    public function findAdvertisement($data,$fields = '*'){
        $where = [];
        if(empty($data['id'])){
            return false;
        }
        $where['id']=$data['id'];
        $result=db_find(self::$tableName,$fields ,$where);
        return $result;
    }
}