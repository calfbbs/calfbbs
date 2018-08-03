<?php
/**
 * Created by PhpStorm.
 * User: Longer
 * Date: 2018/04/22
 * Time: 上午11:23
 */
namespace Addons\admin\controller;

use Addons\admin\controller\Base;

class Modules extends Base
{
    const RESPONSE_SUCCESS = 1001;//请求成功
    const RESPONSE_FAILURE = 2001;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 插件展示页
     *
     */
    public function modulesList()
    {
        global $_G;
        $route = \Framework\library\conf::all('route');

        @$param['current_page'] = $_GET['current_page'] ? $_GET['current_page'] : 1;
        @$param['page_size'] = $_GET['page_size'] ? $_GET['page_size'] : 12;

        $param['sort']="DESC";
        if(!empty($_GET['name'])){
            $param['name'] = $_GET['name'] ;
        }
        $data       = $this->get(url("api/modules/getModuleLists"), $param);
        $list       = "";
        $pagination = "";
        if ($data->code == self::RESPONSE_SUCCESS && $data->data) {
            $list       = $data->data->list;
            $pagination = $data->data->pagination;
        }

        $this->assign('pagination', $pagination);
        $this->assign('list', $list);
        $this->assign('route', $route);
        $this->display('modules/lists');
    }

    /**
     * 未安装展示页
     */
    public function uninstallModulesList()
    {
        //先查出目录下的目录名，然后去数据的dir_name  做比对 去掉admin api install
        //如果不同，把信息显示在未安装列表  最下角加一个"安装"按钮
        global $_G;
        $route = \Framework\library\conf::all('route');
        $conf = CALFBB.'/'.$route['DEFAULT_ADDONS'];

        $dir = scandir($conf);
        $list = [];
        foreach ($dir as $key=>$value)
        {
            if ($value === '.' || $value === '..' || $value === '.DS_Store' || $value === 'admin' || $value === 'api' || $value === 'install' ||  $value === 'plugin'){
                unset($dir[$key]);
            }
        }

        //1、查找数据库中的dir_name  与$dir 进行比对 没有在$dir时，直接挑出来，循环遍历到界面，提示安装
        //2、$dir 循环去数据库查找 ，如果没有得到值，把它赋值给一个数组，然后遍历循环  如果没有数据，去读取模块信息，赋值给一个数组，统一遍历到前端
        foreach ($dir as $key => $value)
        {

            $moduleInfo = $this->post(url("api/modules/getModules"), ['dir_name' => $value]);
            $arr = (array)$moduleInfo;
            $modules = (array)$arr['data'];
            $modules = $modules['modules'];
            if (empty($modules)) {
                $info = $this->getModule('', $value, 'info');
                $infos['name'] = $info['MODULESNAME'];
                $infos['dir_name'] = $info['MODULESDIRNAME'];
                $infos['author'] = $info['MODULESAUTHOR'];
                $infos['desc'] = $info['MODULESDESC'];
                $infos['logo'] = $info['MODULESLOGO'];
                $infos['dbname'] = $info['MODULESDBNAME'];
                $infos['version'] = $info['MODULESVERSION'];
                $list[] = $infos;
            }
        }

        //得到所有的数据，如果有搜索，在这些数据里面搜索，展示，没有搜索根据每页大小展示，搜索时用插件名称
        $this->assign('list', $list);
        $this->assign('route', $route);
        $this->display('modules/uninstallLists');
    }

    /**
     * 检测插件是否安装，没有安装直接给他安装
     *
     */
    public function checkModules()
    {
        global $_G;
        $dir_name = $_GET['dir_name'];
        //获取插件的相关信息
        $modules = $this->getModule('MODULESNAME', $dir_name, 'info');
        if (!$modules) {
            $this->error(url("admin/modules/modulesList"),'未读取到该插件的相关信息，请检查info.php文件！');
        }
        //从info.php获取插件中配置信息
        //$data = $this->post(url("api/Modules/getModules"),['name' => $modules['MODULESNAME']]);
        $data = $this->post(url("api/Modules/getModules"),['dir_name' => $modules['MODULESDIRNAME']]);

        if($data->code==1001 && $data->data){
            $this->success(url("admin/modules/modulesList"),'该插件已经安装');
        }else{
            //安装插件
            $modulesArr = [
                'name' => $modules['MODULESNAME'],
                'author' => $modules['MODULESAUTHOR'],
                'descr' => $modules['MODULESDESC'],
                'logo' => $modules['MODULESLOGO'],
                'version' => $modules['MODULESVERSION'],
                'dir_name' => $modules['MODULESDIRNAME'],
            ];
            $data = $this->post(url('api/modules/addModules'), $modulesArr);

            if ($data->code == 1001) {
                //运行install.sql
                $this->executeSql($modules['MODULESDIRNAME'], 'install.sql');
                $this->success(url("admin/modules/modulesList"),'插件安装成功！');
            } else {
                $this->error(url("admin/modules/modulesList"),'插件安装失败！');
            }
        }
    }

    /**
     * 卸载插件
     */
    public function  uninstallModules()
    {
        global $_G;
        $dir_name = $_GET['dir_name'];
        //获取插件的相关信息
        $modules = $this->getModule('MODULESNAME', $dir_name, 'info');

        if(empty($dir_name)){

            $this->error(url("admin/modules/modulesList"),'错误的插件信息');
        }

        $data=$this->get(url("api/modules/delModules",['dir_name'=>$_GET['dir_name']]));

        if($data->code==1001 && $data->data){
            //运行unstall.sql
            $this->executeSql($modules['MODULESDIRNAME'], 'uninstall.sql');
            $this->success(url("admin/modules/modulesList"),'卸载成功');
        }else{
            $this->error(url("admin/modules/modulesList"),'卸载失败');
        }
    }


    /**
     * 读取插件中的app.conf信息
     * @param $name
     * @param $modules
     * @param string $file
     * @return bool|mixed
     */
    public function getModule($name,$modules,$file="app"){
        $route = \Framework\library\conf::all('route');
        $conf = CALFBB.'/'.$route['DEFAULT_ADDONS'].'/'.$modules."/".$file.'.php';

        if(is_file($conf)) {
            return include $conf;
        }
        return false;
    }

    /**
     * 确认创建数据库、表
     */
    public function executeSql($modules, $sqlfile){

        $database = \Framework\library\conf::all('database');
        $mysqli =@  new \mysqli($database['server'], $database['username'], $database['password'], $database['database_name'], $database['port']);

        if($mysqli->connect_error) {
            echo '数据库连接失败';return;
        }

        if($mysqli->error) {
            $install_error = $mysqli->error;
            echo $install_error;
            return;
        }

        $mysqli->select_db($database['database_name']);
        $mysqli->set_charset($database['charset']);
        $sql = file_get_contents(CALFBB.'/addons/'.$modules."/".$sqlfile);

        $sql = str_replace("\r\n", "\n", $sql);

        $this->runQuery($sql,$database['prefix'],$mysqli);

    }

    /** 执行sql语句
     * @param $sql
     * @param $db_prefix
     * @param $mysqli
     */
    protected function runQuery($sql, $prefix, $mysqli) {
        if(!isset($sql) || empty($sql)) return;
        $sql = str_replace("\r", "\n", str_replace('#__', $prefix, $sql));

        $ret = array();
        $num = 0;
        foreach(explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);
        foreach($ret as $query) {
            $query = trim($query);
            if($query) {
                if(substr($query, 0, 12) == 'CREATE TABLE') {
                    $line = explode('`',$query);
                    $data_name = $line[1];
                    //$this->showMessage('数据表  '.$data_name.' ... 创建成功');
                    $mysqli->query("DROP TABLE IF EXISTS `". $data_name ."`;");
                    $mysqli->query($query);
                    unset($line,$data_name);
                } else {
                    $mysqli->query($query);
                }
            }
        }
    }


}