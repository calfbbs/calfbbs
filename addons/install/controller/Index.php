<?php
/**
 * @className：插件应用路由文件
 * @description：首页入口，文章页入口，公告页入口，用户中心入口
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace Addons\install\controller;

use  Framework\library\View;
use Framework\library\File;
class Index
{
    use View;

    public function __construct()
    {
        //判断是否已安装
        if(is_file(CALFBB."/data/install.lock") && A !="installSuccess"){

             p("系统已经安装过,如果您需要重新安装，请删除data目录下面的install.lock文件");
        }
        $htmlTitle = 'Calfbbs系统安装向导';
        $this->assign('htmlTitle',$htmlTitle);
    }

    /**
     * 安装入口 阅读协议
     * @return string
     */
    public function index(){
        $this->display('index/index');
    }


    /**
     *  检查环境及配置
     */
    public function check(){

        require(APP.'include/function.php');
        require(APP.'include/var.php');
        env_check($envTtems);
        dirfile_check($dirfileTtems);
        function_check($funcItems);
        extension_check($extensionItems);

        $this->assign('envTtems',$envTtems);
        $this->assign('dirfileTtems',$dirfileTtems);
        $this->assign('funcItems',$funcItems);
        $this->assign('extensionItems',$extensionItems);
        $this->display('index/check');
    }

    /**
     * 选中安装方式
     */
    public function select(){
        $this->display('index/select');
    }

    /**
     * 创建数据库信息
     */
    public function createDb(){
        /**
         * 如果进行了提交
         */
        $install_error="";
        $install_recover="no";//是否强制安装覆盖原有数据
        $mysqli="";
        $dbData=[];

        if(@$_POST['submitform']=='submit'){
            $dbData['database_type']='mysql';
            $dbData['database_name']=$_POST['database_name'];
            $dbData['server']=$_POST['server'];
            $dbData['username']=$_POST['username'];
            $dbData['password']=$_POST['password'];
            $dbData['charset']='utf8';
            $dbData['port']=$_POST['port'];
            $dbData['prefix']=$_POST['prefix'];
            $this->createDatabase($dbData,$install_error,$install_recover,$mysqli);

            if(empty($install_error)){
                $install_error=$this->writeConfig($dbData);
                if(!$install_error){
                    $this->display('index/install');
                    $this->submit($dbData,$mysqli);
                    return;
                }

            }
        }

        if(empty($dbData)){
            $dbData['database_type']='mysql';
            $dbData['database_name']='calfbbs';
            $dbData['server']='127.0.0.1';
            $dbData['username']='root';
            $dbData['password']="";
            $dbData['charset']='utf8';
            $dbData['port']="3306";
            $dbData['prefix']="calf_";
            $dbData['admin']='admin';
        }
            $this->assign('dbData',$dbData);
            $this->assign('install_recover',$install_recover);
            $this->assign('install_error',$install_error);
            $this->display('index/createdb');

    }

    /**
     * 安装成功
     */
    public function installSuccess(){

            $this->display('index/success');
    }



    /**
     * 确认创建数据库
     */
    public function submit($dbData,$mysqli){
        $mysqli->select_db($dbData['database_name']);
        $mysqli->set_charset($dbData['charset']);
        $sql = file_get_contents(APP."/data/calfbbs.sql");

        //判断是否安装测试数据
        if (@$_POST['demo_data'] == 1){
            $sql .= file_get_contents(APP."/data/calfbbs_add.sql");
        }
        $sql = str_replace("\r\n", "\n", $sql);

        $this->runQuery($sql,$dbData['prefix'],$mysqli);

        $this->showMessage('初始化数据 ... 成功 ');

        /**
         * 转码
         */
        $token=random(6);
        $user['username'] = $_POST['admin'];
        $user['password'] = md5($token.$_POST['admin_password']);;

        //管理员账号密码
        $data=$mysqli->query("INSERT INTO {$dbData['prefix']}user (`username`,`password`,`token`,`create_time`,`status`,`avatar`) VALUES ('{$user['username']}','". $user['password'] ."','{$token}', '".time()."','2','avatar/default/boy1.jpg')");


        if($data){
            //新增一个标识文件，用来屏蔽重新安装
            if(!is_file(CALFBB."/data/".'install.lock')){
                $fp = @fopen(CALFBB."/data/".'install.lock','wb+');
                @fclose($fp);
            }
        }else{
           // return "管理员增加失败,请检查user表是否存在";
        }


        echo "<script type=\"text/javascript\">document.getElementById('install_process').innerHTML = '安装完成，下一步';document.getElementById('install_process').href='".url('install/index/installSuccess')."';</script>";

    }


    /** 开始创建数据库
     * @param $dbData
     */
    public function createDatabase($dbData,&$install_error,&$install_recover,&$mysqli){

        if (!$dbData['database_name'] || !$dbData['server'] || !$dbData['username'] || !$dbData['password'] || !$dbData['prefix'] || !$dbData['port'] || !$_POST['admin'] || !$_POST['password']){
            $install_error = '输入不完整，请检查';
        }

        if(strpos($dbData['prefix'], '.') !== false) {
            $install_error .= '数据表前缀为空，或者格式错误，请检查';
        }

        if(strlen($_POST['admin']) > 15 || preg_match("/^$|^c:\\con\\con$|　|[,\"\s\t\<\>&]|^游客|^Guest/is", $_POST['admin'])) {
            $install_error .= '非法用户名，用户名长度不应当超过 15 个英文字符，且不能包含特殊字符，一般是中文，字母或者数字';
        }
        if ($install_error != '') return $install_error;


        $mysqli =@  new \mysqli($dbData['server'], $dbData['username'], $dbData['password'], '', $dbData['port']);

        if($mysqli->connect_error) {
            $install_error = '数据库连接失败';return;
        }

        if($mysqli->error) {
            $install_error = $mysqli->error;return;
        }


        if($_POST['install_recover'] != 'yes' && ($query = $mysqli->query("SHOW DATABASES"))) {
            /**
             * 判断数据表是否存在
             */
          /*  while($row = mysqli_fetch_array($query)) {
                if(preg_match("/^{$dbData['prefix']}/", $row[0])) {
                    $install_error = '数据表已存在，继续安装将会覆盖已有数据';
                    $install_recover = 'yes';
                    return;
                }
            }*/

            /**
             * 判断数据库是否存在
             */
                while($row = mysqli_fetch_array($query)) {
                    if(preg_match("/^{$dbData['database_name']}/", $row[0])) {
                        $install_error = '数据库已存在，继续安装将会覆盖已有数据';
                        $install_recover = 'yes';
                        return;
                    }
                }

        }


        if($mysqli->get_server_info()> '5.0') {
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbData['database_name']}` DEFAULT CHARACTER SET ".$dbData['charset']);
        } else {
            $install_error = '数据库必须为MySQL5.0版本以上';return;
        }


    }

    /** 将数据库配置写入配置文件
     * @param $dbData
     *
     * @return string
     */
    protected function writeConfig($dbData){
        $text=CALFBB."/data/database.php";

        if(!is_writable($text) && is_file($text)){
          return  $install_error=$text."没有可写权限";
        }

        $file=new File();
        $saveText="<?php return ".var_export($dbData,true).";";

        $saveText=$file->file_write($text,$saveText,0777);
        if(!$saveText){
            return  $install_error=$text."数据库配置写入失败";
        }
    }


    /** 执行sql语句
     * @param $sql
     * @param $db_prefix
     * @param $mysqli
     */
    protected function runQuery($sql, $db_prefix, $mysqli) {
        if(!isset($sql) || empty($sql)) return;
        $sql = str_replace("\r", "\n", str_replace('#__', $db_prefix, $sql));

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
                    $this->showMessage('数据表  '.$data_name.' ... 创建成功');
                    $mysqli->query("DROP TABLE IF EXISTS `". $data_name ."`;");
                    $mysqli->query($query);
                    unset($line,$data_name);
                } else {
                    $mysqli->query($query);
                }
            }
        }
    }

    /**
     * @param $message
     */
    protected function showMessage($message) {
        echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
        flush();
        ob_flush();
    }
}