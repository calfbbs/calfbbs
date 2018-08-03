<?php
/**
 * @className：入口路由文件
 * @description：首页入口，文章页入口，公告页入口，用户中心入口
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Index  extends Base
{



    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 首页入口
     * @return string
     */
    public function index(){
        global $_G;

        $this->assign('SERVER_PORT',$_SERVER['SERVER_PORT']);
        $this->assign('SERVER_SOFTWARE',$_SERVER['SERVER_SOFTWARE']);
        $this->assign('HTTP_ACCEPT_LANGUAGE',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $this->assign('PHP_UNAME',php_uname('s'));
        $this->assign('PHP_VERSION',PHP_VERSION);
        $this->assign('Zend_Version',Zend_Version());
        $this->display('index/index');
    }

    /**
     * 设置入口
     * @return string
     */
    public function setting(){
        global $_G;

        $this->assign('SERVER_PORT',$_SERVER['SERVER_PORT']);
        $this->assign('SERVER_SOFTWARE',$_SERVER['SERVER_SOFTWARE']);
        $this->assign('HTTP_ACCEPT_LANGUAGE',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $this->assign('PHP_UNAME',php_uname('s'));
        $this->assign('PHP_VERSION',PHP_VERSION);
        $this->assign('Zend_Version',Zend_Version());
        $this->display('index/index');
        $this->display('index/setting');
    }

}