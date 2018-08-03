<?php
/**
 * @className：插件接口数据模型
 * @description：查询插件，插入插件，删除插件
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\model;

use Addons\api\model\BaseModel;
class ModulesModel  extends BaseModel
{
    /**
     * @var string $tableName 插件表
     */
    private static  $tableName="modules";

    /**
     * 获取某个插件信息
     * */
    public function getOne($where){

        $res = db_select(self::$tableName,$fields = "*",$where);
        //$debug = db_sql(true);
        return $res;
    }


    /**
     * 获取某个插件列表信息
     * */
    public function getModuleList($data, $column = '*'){
        $sql = 'SELECT ' . $column . ' FROM ' . table_prefix(self::$tableName)." where 1=1 ";

        /**
         * 条件
         */

        $where=[];
        if ( !empty($data['name'])) {
            $sql             .= ' AND name LIKE :name';
            $where[':name'] = "%" . $data['name'] . "%";
        }

        /**
         * 利用索引进行分页 倒序时不可用
         */
        //$sql .= ' and p.id > '.($data['current_page'] - 1) * $data['page_size'] ;

        /**
         * 排序
         */
        if ( !empty($data['orderBy']) && !empty($data['sort'])) {
            $sql .= ' ORDER BY p.' . $data['orderBy'] . ' ' . $data['sort'];
        }


        /**
         * 限制条数
         */
        $sql .= ' LIMIT ' . ($data['current_page'] - 1) * $data['page_size'] . "," . $data['page_size'];

        return db_query($sql, $where);


    }



    /**
     * 统计帖子数据总条数
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function countModules($data)
    {

        $sql = 'SELECT count(*) AS num FROM ' . table_prefix(self::$tableName);

        $where=[];
        if ( !empty($data['name'])) {
            $sql             .= ' where name LIKE :name';
            $where[':name'] = "%" . $data['name'] . "%";
        }

        return db_fetch($sql, $where)['num'];
    }

    /**
     * 插入一条modules数据
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function insertModules($data)
    {
        $result = db_insert(self::$tableName, $data);
        //$debug = db_sql(true);
        return $result;
    }

    /** 删除一条插件数据
     * @param $where 传入数据条件
     * @return int | bool $result
     */
    public function delModule($where){
        $result=db_delete(self::$tableName,$where);
        return $result;
    }

    /**查找单条插件数据
     * @param  array      $data
     * @param string $fields
     *
     * @return \Ambigous|string
     */
    public function findModules($data,$fields = '*'){

        $where = [];
        if(empty($data['dir_name'])){
            return false;
        }
        $where['dir_name']=$data['dir_name'];
        $result=db_find(self::$tableName,$fields ,$where);
        return $result;
    }
}