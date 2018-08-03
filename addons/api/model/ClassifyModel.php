<?php
/**
 * @className：无限分类接口数据模型
 * @description：增加分类，删除分类，编辑分类，分类展示
 * @author:calfbbs技术团队
 * Date: 2017/10/25
 * Time: 下午6:25
 */

namespace Addons\api\model;
use Addons\api\model\BaseModel;
class ClassifyModel extends BaseModel
{
    /**
     * @var string $tableName 无限分类表名
     */
    private static  $tableName="classify";
    /** 插入一条数据到表
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function insertClassify($data){

        //① 顶级权限：等于 “主键id值”
        if($data['pid'] == 0){
            $info = [
                'name' => $data['name'],
                'pid' => $data['pid'],
                'path' => 0,
                'level' => 1,
                'create_time' => time(),
            ];
            $id = db_insert(self::$tableName, $info);
            $where['path'] = $id;
        }else{
        //② 非顶级权限：等于 “父级全路径-主键id值”
            $pinfo = db_find(self::$tableName,'*',['id' => $data['pid']]);
            $info = [
                'name' => $data['name'],
                'pid' => $data['pid'],
                'path' => 0,
                'level' => $pinfo['level']+1,
                'create_time' => time(),
            ];
            $id = db_insert(self::$tableName, $info);
            $where['path'] = $pinfo['path'].'-'.$id;
        }
        $result = db_update(self::$tableName, $where, array('id' => $id));
        return $result;
    }


    /**获取分类列表   带分页
     * @return int | bool $result
     */
    public function selectClassify($data){
        $result=db_select(self::$tableName,$fields = "*",$where = [],$data['current_page'], $data['page_size'],$orderby=['path' =>'ASC']);
        return $result;
    }

    /** 不带分页
     * @function
     * @return mixed
     */
    public function getClassify(){
        $result = db_select(self::$tableName,$fields = '*',$where=[],$page='',$pagesize='',$orderby=[]);
        return $result;
    }

    /**查找单条分类数据
     * @param  array      $data
     * @param string $fields
     *
     * @return \Ambigous|string
     */
    public function findClassify($data,$fields = '*'){
        $where = [];
        if(empty($data['id'])){
            return false;
        }
        $where['id']=$data['id'];
        $result=db_find(self::$tableName,$fields ,$where);
        return $result;
    }

    /** 删除某一分类
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function deleteClassify($data){
        $ids = $data['id'];
//        $subNodes = array();          //删除分类同时删除子分类
//        foreach (explode ( ',', $ids ) as $k){
//            $res = $this->getClassifyNode($k,$subNodes[$k]);  //获取子节点
//            if(!empty($res)){
//                foreach($res as $k => $nid){
//                    db_delete(self::$tableName, array('id' => $nid));
//                }
//            }
//        }
        $arr = db_find(self::$tableName,"*",['pid'=>$data['id']]);
        if(!empty($arr)){
            return false;
        }else{
            $result = db_delete(self::$tableName, array('id' => $ids));
            return $result;
        }

    }


    /** 修改某一分类
     * @param $data 传入数据
     * @return int | bool $result
     */
    public function updateClassify($data){
        $ids = $data['id'];
        if($data['pid'] == 0){
            $data['level'] = 1;
            $data['path'] = $ids;
        }else{
            $pinfos = db_find(self::$tableName,"*",['id'=>$data['pid']]);
            $data['level'] = $pinfos['level']+1;
            $data['path'] = $pinfos['path'].'-'.$ids;
        }
        $result = db_update(self::$tableName,$data, array('id' => $ids));
        $subNodes = array();
        foreach (explode ( ',', $ids ) as $k){
            $res = $this->getClassifyNode($k,$subNodes[$k]);  //获取子节点
            if(!empty($res)){
                foreach($res as $k => $nid){
                    $info = db_find(self::$tableName,"*",['id'=>$nid]);
                    $pinfo = db_find(self::$tableName,"*",['id'=>$info['pid']]);
                    $map = [
                        'path' => $pinfo['path'].'-'.$nid,
                        'level' => $pinfo['level']+1,
                    ];
                    db_update(self::$tableName,$map, array('id' => $nid));
                }
            }
        }
        return $result;
    }


    //获取子分类
    public function getClassifyNode($id,&$arr){
        $ret =  db_select(self::$tableName,$fields="*",$where=['pid' => $id]);
        if(!empty($ret)){
            foreach ($ret as $k => $node){
                $arr[] = $node['id'];
                $this->getClassifyNode($node['id'], $arr);
            }
        }
        return $arr;
    }



    /** 统计分类数据总条数
     * @function
     * @return mixed
     */
    public function countClassify($data){
        $result = db_count(self::$tableName);
        return $result;
    }



    /**分页信息
     * @function
     * @param $page_size    每页显示数量
     * @param $current_page 当前页码
     * @param $count    总条数
     * @return mixed
     */
    public function getPagination($page_size, $current_page, $count)
    {
        $pagination['total']        = (int)$count;
        $pagination['page_count']   = $count > 0 ? ceil($count / $page_size) : 0;
        $pagination['current_page'] = (int)$current_page;
        $pagination['page_size']    = (int)$page_size;

        return $pagination;
    }

    /**获取默认分页参数
     * @function
     * @param $data 分页预处理数据
     * @return mixed
     */
    public function getDefaultPage($data)
    {
        $data['page_size']    = empty($data['page_size']) ? 10 : $data['page_size'];
        $data['current_page'] = empty($data['current_page']) ? 1 : $data['current_page'];
        $data['sort']         = empty($data['sort']) ? 'ASC' : $data['sort'];

        return $data;
    }



}