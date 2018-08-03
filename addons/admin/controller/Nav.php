<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/8
 * Time: 上午10:46
 */

namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Nav extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 导航列表
     */
    public function navList(){
        global $_G;

        @$param['current_page'] = $_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size'] = $_GET['page_size'] ? $_GET['page_size'] : 10;
        
        $data = $this->get(url("api/Nav/getNavList"),$param);

        $list="";
        $pagination="";
        if($data->code==1001 && $data->data){
            $list=$data->data->list;
            $pagination=$data->data->pagination;
        }
        $this->assign('pagination',$pagination);
        $this->assign('list',$list);
        $this->display('nav/list');
    }
    /**
     * 添加导航
     */
    public function add(){
        global $_G,$_GPC;

        if(@$_GPC['submit']){
            /**
             * 限制只允许添加6条导航
             */
            $list= $this->get(url("api/Nav/getNavList"),['current_page'=>1,'page_size'=>10]);
           
            if($list->code==1001 && count($list->data->list) >= 6){
                $this->error(url("admin/Nav/add"),"导航目前只支持创建6条记录,需要更多请联系calfbbs团队");
                return;
            }
            $data=$this->get(url("api/Nav/addNav"),$_POST);

            if($data->code==1001 && $data->data){
                $this->success(url("admin/Nav/navList"));
            }else{
                $meassge="失败";
                if(isset($data->data->path)){
                    $meassge="导航栏必须是一个完整的url";
                }
                $this->error(url("admin/Nav/add"),$meassge);
            }
        }


        $this->display('nav/add');
    }

    /**
     * 编辑导航
     */
    public function edit(){
        global $_G,$_GPC;
        /**
         * 如果有编辑广告
         */

        if(@$_GPC['submit']){

            $data=$this->get(url("api/Nav/changNav"),$_POST);

            if($data->code==1001 && $data->data){
                $this->success(url("admin/Nav/navList"));
            }else{
                $meassge="失败";
                if(isset($data->data->path)){
                    $meassge="广告地址必须是一个完整的url";
                }
                $this->error(url("admin/Nav/edit","id=".$_POST['id']),$meassge);
            }
        }

        $data=$this->get(url("api/Nav/getNavOne","id=".$_GET['id']));
        $this->assign('data',$data->data);
        $this->display('nav/edit');
    }
    /**
     * 删除导航栏
     */
    public function delete(){
        global $_G;
      
        if(empty($_GET['id'])){

            $this->error(url("admin/avv/navList"),'错误的删除链接');
        }

        $data=$this->get(url("api/nav/delNav",['id'=>$_GET['id']]));

        if($data->code==1001 && $data->data){

            $this->success(url("admin/nav/navList"),'删除成功');
        }else{
            $this->error(url("admin/nav/navList"),'删除失败');
        }
    }
}