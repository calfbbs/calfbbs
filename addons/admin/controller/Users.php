<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/8
 * Time: 上午10:56
 */

namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Users extends Base
{
    const ADMIN_USERS_LIST = 'admin/users/userList';
    const RESPONSE_SUCCESS = 1001;//请求成功
    const RESPONSE_FAILURE = 2001;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 用户列表
     */
    public function userList(){
        global $_G;

        @$param['current_page']=$_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size']=$_GET['page_size'] ? $_GET['page_size'] : 10;
        
        if(@$_GET['kw']){
            @$param['username']=$_GET['kw'];
            @$param['email']=$_GET['kw'];
            $data       = $this->get(url("api/user/getSearchList"), $param);
        }else{
        
            $data       = $this->get(url("api/user/getUsersList"), $param);
        }
        $list       = "";
        $pagination = "";
        if ($data->code == self::RESPONSE_SUCCESS && $data->data) {
            $list       = $data->data->list;
            $pagination = $data->data->pagination;
        }
        $this->assign('pagination', $pagination);
        $this->assign('list', $list);    
        $this->assign('kw', @$_GET['kw']);    

        
        $this->display('users/list');
    }    
    
    /**
     * 添加用户
     */
    public function add()
    {
        if ( !empty($_POST['submit'])) { 
            $data = $this->post(url("api/user/addUser"), $_POST);

            if ($data->code == self::RESPONSE_SUCCESS && $data->data)
                $this->success(url(self::ADMIN_USERS_LIST));
            $this->error(url("admin/users/add"), $this->getError($data->data));
        }
        
        $this->display('users/add');
    }

    /**
     * 编辑用户
     */
    public function edit()
    {
        global $_G;
        /**
         * 编辑
         */
        if ( !empty($_POST['submit'])) {
            $data = $this->post(url("api/user/updateUser"), $_POST);

            if(!empty($_POST['password']))
            {
                $_data['uid'] = $_POST['uid'];
                $_data['password'] = $_POST['password'];
                $response = $this->post(url("api/user/adminModifyPassword"), $_data);
            }

            if ($data->code == self::RESPONSE_SUCCESS)
                $this->success(url(self::ADMIN_USERS_LIST,['current_page'=>$_POST['current_page']],false));
            $this->error(url("admin/users/edit", "uid=" . $_POST['uid']), $this->getError($data->data));
        }

        /**
         * 详情
         */
        if (empty($_GET['uid']))
            $this->error(url(self::ADMIN_USERS_LIST), '用户uid不能为空');
        @$current_page=$_GET['current_page'];

        $data = $this->get(url("api/user/getUserInfo", "uid=" . $_GET['uid']));

        if ($data->code == self::RESPONSE_FAILURE)
            $this->error(url(self::ADMIN_USERS_LIST), $this->getError($data->data));

        

        $this->assign('data', $data->data);
        $this->assign('current_page', $current_page);

        $this->display('users/edit');
    }


    /**
     * 删除用户
     */
    public function delete()
    {
        global $_G;
        // 删除单条
        if (empty($_POST)) {
            @$current_page=$_GET['current_page'];
            empty($_GET['uid']) && $this->error(url(self::ADMIN_USERS_LIST,['current_page'=>$current_page]), '错误的删除链接');
            $data = $this->get(url("api/user/deleteUser"), ['uid' => $_GET['uid']]);            
            if ($data->code == self::RESPONSE_SUCCESS && $data->data){            
                $data_avatar = $this->get(url("api/files/deleteFile", ['path' => $_GET['avatar']]));
            }

        } else {
            // 删除多条
            @$current_page=$_POST['current_page'];
            empty($_POST['deletes']) && $this->error(url(self::ADMIN_USERS_LIST,['current_page'=>$current_page]), '错误的删除链接');
            $file=new \Framework\library\File();
        
            foreach ($_POST['deletes'] as $key=>$uid) {
                is_numeric($uid) || $this->error(url(self::ADMIN_USERS_LIST,['current_page'=>$current_page]), '删除的用户uid必须是数字');
                $data = $this->get(url("api/user/deleteUser"), ['uid' => $uid]);
                if ($data->code == self::RESPONSE_SUCCESS){
                    $path=$_POST['deletes_avatar'][$key];
                    $data_avatar=$file->file_delete($path);
                    //$data_avatar = $this->get(url("api/files/deleteFile", ['path' => $path]));//in post function can't use get                    
                } else{
                    break;
                }
            }            
        }

        if ($data->code == self::RESPONSE_SUCCESS && $data->data){
            //if ($data_avatar->code == self::RESPONSE_SUCCESS && $data_avatar->data){  
                $this->success(url(self::ADMIN_USERS_LIST,['current_page'=>$current_page]), '删除成功');
            // }else{
            //     $str='删除数据库成功,'.$this->getError($data_avatar->data);
            //     $this->success(url(self::ADMIN_USERS_LIST), $str);
            // }
        }

        $this->error(url(self::ADMIN_USERS_LIST), $this->getError($data->data));
    }


    /**
     * 获取错误信息
     */
    public function getError($error)
    {
        foreach ($error as $key => $message) {
            return $key . $message;
        }
    }
    /**
     * @function 富文本编辑器图片 上传
     */
    public function doUploadPic()
    {
        global $_G;

        $res = $this->post(url("api/files/uploadFile"), ['file' => $_FILES['wangEditorH5File']]);
        echo ($res->code == self::RESPONSE_SUCCESS ? $_G['ATTACHMENT_ROOT'] . '/' : 'error|') . $res->data;
    }
}