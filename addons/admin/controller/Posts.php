<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/7
 * Time: 下午7:05
 */

namespace Addons\admin\controller;

/**
 * Class Posts
 * @package Addons\admin\controller
 */
class Posts extends Base
{
    const ADMIN_POSTS_LIST = 'admin/posts/postList';
    const RESPONSE_SUCCESS = 1001;//请求成功
    const RESPONSE_FAILURE = 2001;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 帖子列表
     */
    public function postList()
    {
        @$param['current_page'] = $_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size'] = $_GET['page_size'] ? $_GET['page_size'] : 10;
        //$param['admin_id'] = self::$userinfo['uid'];
        @$param['title'] = $_GET['title'];
        $param['sort']="DESC";
        $data       = $this->post(url("api/post/getPostListByAdmin"), $param);
        $list       = "";
        $pagination = "";
        if ($data->code == self::RESPONSE_SUCCESS && $data->data) {
            $list       = $data->data->list;
            $pagination = $data->data->pagination;
        }
        $this->assign('pagination', $pagination);
        $this->assign('list', $list);

        $this->display('posts/list');
    }

    /**
     * 添加帖子
     */
    public function add()
    {
        if ( !empty($_POST['submit'])) {
            $_POST['uid'] = self::$userinfo['uid'];

            $data = $this->post(url("api/post/addPost"), $_POST);

            if ($data->code == self::RESPONSE_SUCCESS && $data->data)
                $this->success(url("admin/posts/postList"));
            $this->error(url("admin/posts/add"), $this->getError($data->data));
        }

        $clssify = $this->getClassify();
        $this->assign('clssify', $clssify);

        $this->display('posts/add');
    }

    /**
     * 编辑帖子
     */
    public function edit()
    {
        /**
         * 编辑
         */
        if ( !empty($_POST['submit'])) {

            $_POST['edit_uid'] = self::$userinfo['uid'];

            $data = $this->post(url("api/post/changePost"), $_POST);

            if ($data->code == self::RESPONSE_SUCCESS && $data->data)
                $this->success(url("admin/posts/postList"));
            $this->error(url("admin/posts/edit", "id=" . $_POST['id']), $this->getError($data->data));
        }

        /**
         * 详情
         */
        if (empty($_GET['id']))
            $this->error(url(self::ADMIN_POSTS_LIST), '帖子id不能为空');

        $data = $this->get(url("api/post/getPost", "id=" . $_GET['id']));

        if ($data->code == self::RESPONSE_FAILURE)
            $this->error(url(self::ADMIN_POSTS_LIST), $this->getError($data->data));

        $category = $this->getClassify();

        $this->assign('data', $data->data);
        $this->assign('clssify', $category);

        $this->display('posts/edit');
    }


    /**
     * 删除帖子
     */
    public function delete()
    {
        // 删除单条
        if (empty($_POST)) {

            empty($_GET['id']) && $this->error(url(self::ADMIN_POSTS_LIST), '错误的删除链接');

            $data = $this->post(url("api/post/delPost"), ['id' => $_GET['id'], 'uid' => @self::$userinfo['uid']]);
        } else {

            // 删除多条
            empty($_POST['deletes']) && $this->error(url(self::ADMIN_POSTS_LIST), '错误的删除链接');

            foreach ($_POST['deletes'] as $id) {
                is_numeric($id) || $this->error(url(self::ADMIN_POSTS_LIST), '删除的帖子id必须是数字');
                $data = $this->post(url("api/post/delPost"), ['id' => $id, 'uid' => @self::$userinfo['uid']]);
                if ($data->code != self::RESPONSE_SUCCESS) break;
            }
        }

        if ($data->code == self::RESPONSE_SUCCESS && $data->data)
            $this->success(url(self::ADMIN_POSTS_LIST), '删除成功');

        $this->error(url(self::ADMIN_POSTS_LIST), $this->getError($data->data));
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
     * @function 获取一级分类
     * @return array
     */
    public function getClassify()
    {
        $classify = $this->get(url('api/classify/getClassifylist'));

        if ($classify->code == self::RESPONSE_FAILURE)
            $this->error(url(self::ADMIN_POSTS_LIST), '帖子类别' . $classify->message);

        $category = [];

        if ( !empty($classify->data))
            foreach ($classify->data as $val) {
                if ($val->pid == 0) $category[] = ['id' => $val->id, 'name' => $val->name];
            }

        return $category;
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