<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/8
 * Time: 上午10:26
 */

namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Classify extends Base
{
    const TRUE_CODE = 1001;

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 分类列表
     */
    public function classifyList(){
        global $_G;
        @$param['current_page']=$_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size']=$_GET['page_size'] ? $_GET['page_size'] : 10;
        $data=$this->get(url("api/classify/getClassifylistAll"),$param);
        $list="";
        $pagination="";
        if($data->code==self::TRUE_CODE && $data->data){
            $list=$data->data->list;
            foreach ($list as $key=>$value){
                $list[$key]->name= str_repeat('——',$value->level-1).$value->name;
            }
            $pagination=$data->data->pagination;
        }
        $this->assign('pagination',$pagination);
        $this->assign('list',$list);
        $this->display('classify/list');
    }


    /**
     * 添加分类
     */
    public function add(){
        global $_G,$_GPC;
        if(@$_GPC['submit']){
            $data=$this->get(url("api/classify/addClassify"),$_POST);
            if($data->code==self::TRUE_CODE && $data->data){
                $this->success(url("admin/classify/classifyList"));
            }else{
                $meassge="添加失败";
                $this->error(url("admin/classify/add"),$meassge);
            }
        }
        $list=$this->get(url("api/classify/getClassifylist"));
        $arr=[];
        if($list->code==self::TRUE_CODE && $list->data){
            $list=$list->data;
            foreach ($list as $value){
                $arr[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                ];
                if(!empty($value->children)){
                    $this->handleClassify($value->children,$arr);
                }
            }
        }
        $this->assign('list',$arr);
        $this->display('classify/add');
    }

    /**
     * 编辑分类
     */
    public function edit(){
        global $_G,$_GPC;
        if(@$_GPC['submit']){
            $data=$this->get(url("api/classify/editClassify"),$_POST);
            if($data->code==self::TRUE_CODE && $data->data){
                $this->success(url("admin/classify/classifyList"));
            }else{
                $meassge="修改失败";
                $this->error(url("admin/classify/edit","id=".$_POST['id']),$meassge);
            }
        }
        $info=$this->get(url("api/classify/getClassifyOne","id=".$_GET['id']));
        $list=$this->get(url("api/classify/getClassifylist"));
        $arr=[];
        if($list->code==self::TRUE_CODE && $list->data){
            $list=$list->data;
            foreach ($list as $value){
                $arr[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                ];
                if(!empty($value->children)){
                    $this->handleClassify($value->children,$arr);
                }
            }
        }
        $this->assign('list',$arr);
        $this->assign('info',$info->data);
        $this->display('classify/edit');
    }

    /**
     * 删除分类
     */
    public function delete(){
        global $_G;
        if(empty($_GET['id'])){

            $this->error(url("admin/classify/classifyList"),'错误的删除链接');
        }
        $data=$this->get(url("api/classify/delClassify",['id'=>$_GET['id']]));

        if($data->code==self::TRUE_CODE && $data->data){

            $this->success(url("admin/classify/classifyList"),'删除成功');
        }else{
            $this->error(url("admin/classify/classifyList"),'删除失败');
        }
        $this->display('classify/list');
    }

    /**使用递归将分类重新排序
     * @function
     * @param $classify
     * @param $data
     * @param int $num
     */
    public function handleClassify($classify,&$data,$num=1){
        foreach ($classify as $value){
            $data[] = [
                'id' => $value->id,
                'name' => str_repeat('-',$num).$value->name,
            ];
            if(!empty($value->children)){
                $this->handleClassify($value->children,$data,$num+1);
            }
        }
    }

}