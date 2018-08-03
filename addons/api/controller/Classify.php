<?php

/**
 * @className：无限分类接口管理
 * @description：增加分类，删除分类，编辑分类，分类展示
 * @author:calfbbs技术团队
 * Date: 2017/10/25
 * Time: 下午6:25
 */
namespace Addons\api\controller;
use Addons\api\model\ClassifyModel;
use Addons\api\validate\ClassifyValidate;
class Classify extends ClassifyModel
{
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();

    }

    /**
     * 添加分类方法
     * @param varchar  $name   分类名称
     * @param bool $cid 绑定分类id
     * @return array $data   响应数据
     */
    public function addClassify(){
        /**
         *get 字段参数验证是否符合条件
         */
        $validate = new ClassifyValidate();
        $validateResult=$validate->addClassifyValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 插入数据到数据库
         */


        $info = db_find("classify", $fields = "*",$where = ['name'=>$validateResult['name']]);
        if($info){
            return $this->returnMessage(1001,'响应成功',"该分类名称已存在");
        }
        $result=$this->insertClassify($validateResult);
        if($result){
            return $this->returnMessage(1001,'响应成功',(int)$result);
        }else{
            return $this->returnMessage(2001,'响应错误',$result);

        }

    }



    /**
     * 删除分类方法（包括删除子分类）
     * @param int id 分类id
     * @return array $data   响应数据
     */
    public function delClassify(){

        $validate = new ClassifyValidate();
        $validateResult=$validate->delClassifyValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 删除数据
         */
        $result=$this->deleteClassify($validateResult);
        if($result){
            return $this->returnMessage(1001,'响应成功',$result);
        }else{
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }


    /**
     * 更新分类
     * @param id  $id   分类id
     * @param varchar  $name   分类名称
     * @return array $data   响应数据
     */
    public function editClassify(){
        /**
         *get 字段参数验证是否符合条件
         */
        $validate = new ClassifyValidate();
        $validateResult=$validate->updateClassifyValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $result=$this->updateClassify($validateResult);
        if($result){

            return $this->returnMessage(1001,'响应成功',(int)$result);
        }else{
            return $this->returnMessage(2001,'响应错误',$result);

        }
    }




    /**
     * 提取分类信息
     * @param varchar  $name   分类名称
     * @param bool $cid 绑定分类id
     * @return array $data   响应数据
     */
    public function getClassifylist()
    {
        $result = $this->getClassify();
//        foreach ($result as $key=>$value){
//            $result[$key]['name'] = str_repeat('--',$value['level']-1).$value['name'];
//        }
        $result = $this->tree($result);   //树形图
        if($result){
            return $this->returnMessage(1001,'响应成功',$result);
        }else{
            return $this->returnMessage(2001,'暂无数据',NULL);
        }
    }


    /** 获取分类列表
     * @function
     */
    public function getClassifylistAll(){

        $_GET = $this->getDefaultPage($this->get);
        $validator = new ClassifyValidate();
        $validateResult = $validator->getClassifyValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $count = $this->countClassify($validateResult);
        $result = "";
        if($count>0){
            $result = $this->selectClassify($validateResult);
        }
        $data['pagination'] = $this->getPagination($validateResult['page_size'],$validateResult['current_page'],$count);
        if($result){
            $data['list']=$result;
            return $this->returnMessage(1001,'响应成功',$data);

        }else{
            $data['list']=[];
            return $this->returnMessage(2001,'暂无数据',$data);
        }

    }


    /**
     * 获取单条分类数据
     * @param int $id 分类数据id
     */
    public function getClassifyOne(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new ClassifyValidate();
        $validateResult=$validate->getClassifyOneValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $result=$this->findClassify($validateResult);

        if($result){
            return $this->returnMessage(1001,'响应成功',$result);

        }else{
            return $this->returnMessage(2001,'暂无数据',null);
        }
    }


    /*
         *无限分类的树状输出
         *@param $tree 排序对象
         *@param $rootId 父id
         */
    public function tree($tree, $rootId = 0) {
        $return = array();
        foreach($tree as $leaf) {
            if($leaf['pid'] == $rootId) {
                foreach($tree as $subleaf) {
                    if($subleaf['pid'] == $leaf['id']) {
                        $leaf['children'] = $this->tree($tree, $leaf['id']);
                        break;
                    }
                }
                $return[] = $leaf;
            }
        }
        return $return;
    }



}