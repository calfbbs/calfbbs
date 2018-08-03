<?php
/**
 * @className：广告相关类
 * @description：广告相关展示界面
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Advertisement extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 广告列表
     */
    public function advertisementList(){

        global $_G;
        @$param['current_page']=$_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size']=$_GET['page_size'] ? $_GET['page_size'] : 10;

        $data=$this->get(url("api/advertisement/getAdvertisementList"),$param);
        $list="";
        $pagination="";
        if($data->code==1001 && $data->data){
            $list=$data->data->list;
            $pagination=$data->data->pagination;
        }
        $this->assign('pagination',$pagination);
        $this->assign('list',$list);
        $this->display('advertisement/list');
    }
    /**
     * 添加广告
     */
    public function add(){
        global $_G,$_GPC;
        /**
         * 如果有添加广告
         */

        if(@$_GPC['submit']){

            $data=$this->get(url("api/advertisement/addAdvertisement"),$_POST);

           if($data->code==1001 && $data->data){
               $this->success(url("admin/advertisement/advertisementList"));
           }else{
               $meassge="失败";
               if(isset($data->data->url_path)){
                   $meassge="广告地址必须是一个完整的url";
               }
               $this->error(url("admin/advertisement/add"),$meassge);
           }
        }

        $this->display('advertisement/add');
    }

    /**
     * 编辑广告
     */
    public function edit(){
        global $_G,$_GPC;
        /**
         * 如果有编辑广告
         */

        if(@$_GPC['submit']){

            $data=$this->get(url("api/advertisement/changeAdvertisement"),$_POST);

            if($data->code==1001 && $data->data){
                $this->success(url("admin/advertisement/advertisementList"));
            }else{
                $meassge="失败";
                if(isset($data->data->url_path)){
                    $meassge="广告地址必须是一个完整的url";
                }
                $this->error(url("admin/advertisement/edit","id=".$_POST['id']),$meassge);
            }
        }

        $data=$this->get(url("api/advertisement/getAdvertisementOne","id=".$_GET['id']));

        $this->assign('data',$data->data);
        $this->display('advertisement/edit');
    }

    /**
     * 删除广告
     */
    public function delete(){
        global $_G;
        if(empty($_GET['id'])){

           $this->error(url("admin/advertisement/advertisementList"),'错误的删除链接');
        }

        $data=$this->get(url("api/advertisement/delAdvertisement",['id'=>$_GET['id']]));

        if($data->code==1001 && $data->data){

            $this->success(url("admin/advertisement/advertisementList"),'删除成功');
        }else{
            $this->error(url("admin/advertisement/advertisementList"),'删除失败');
        }
    }



}