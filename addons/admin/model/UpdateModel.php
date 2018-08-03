<?php
/**
 * Created by PhpStorm.
 * User: rock
 * Date: 2017/11/7
 * Time: 下午7:05
 */
namespace Addons\admin\model;
use Framework\library\File;
class UpdateModel
{
    public static $ini;

    public function __construct()
    {
        if(!self::$ini){
            self::$ini=$this->readini(CALFBB. '/data/version.ini');
        }


    }

    /** ping 服务器
     * @return array|bool
     */
    public function ping($data){

        $data=self::$ini['version'];
        $item=$this->httpsRequest(self::$ini['version']['serverPingApi'],$data);

        return  $item;
    }

    /** 获取版本配置文件
     * @return array|bool
     */
    public  function getVersion(){

        return self::$ini;
    }

    /** 检测服务器版本是否有新文件
     * @param $url
     * @param $data
     *
     * @return mixed
     */
    public function getUpFileList($url,$data){
        $item=$this->httpsRequest($url,$data);

        return  $item;
    }


    /** 获取服务器最新版本
     * @param $url
     * @param $data
     *
     * @return mixed
     */
    public function serverVersion($url,$data){
        $item=$this->httpsRequest($url,$data);

        return  $item;
    }

    /** 执行升级
     * @param $url
     * @param $data
     *
     * @return mixed
     */
    public function actionUpgrade($url,$data){
        $requset=$this->httpsRequest($url,$data);
        $requset=json_decode($requset,true);
        $downloadApi=$data['downloadApi']."/".$data['uid']."/".$data['project_id']."/";

        if($requset['code']==0 && !empty($requset['data']['list'])){
                $list=$requset['data']['list'];
                $file=new File();
                foreach($list as $key=>$value){
                    $filepath=CALFBB."/".$value;
                    $context=file_get_contents($downloadApi.$value);
                    $file->file_write($filepath,$context);
                }
        $this->updateIni($requset['data']['tag'],$requset['data']['newbranch']);
        }
        $result['code']=1001;
        $result['message']="升级完毕";
        $result['data']=true;
        return $result;
    }

    /** 更新配置文件
     * @param bool $tag
     * @param bool $newbranch
     */
    public function updateIni($tag=false,$newbranch=false){
        $ini=self::$ini;
        if($tag){
            $ini['version']['tag']=$tag;
        }
        if($newbranch){
            $ini['version']['branch']=$newbranch;
        }

       $this->write_ini_file($ini,CALFBB. '/data/version.ini',true);
    }

    /**
     * 判断是否需要更新数据库
     */
    public function updateSql(){

    }



    public function readini($name)
    {
        if (file_exists($name)){
            $data = parse_ini_file($name,true);
            if ($data){
                return $data;
            }
        }else {
            return false;
        }
    }


    function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
        $content = "";
        if ($has_sections) {
            foreach ($assoc_arr as $key=>$elem) {
                $content .= "[".$key."]\r\n";
                foreach ($elem as $key2=>$elem2) {
                    if(is_array($elem2))
                    {
                        for($i=0;$i<count($elem2);$i++)
                        {
                            $content .= $key2."[] = ".$elem2[$i]."\r\n";
                        }
                    }
                    else if($elem2=="") $content .= $key2." = \r\n";
                    else $content .= $key2." = ".$elem2."\r\n";
                }
            }
        }
        else {
            foreach ($assoc_arr as $key=>$elem) {
                if(is_array($elem))
                {
                    for($i=0;$i<count($elem);$i++)
                    {
                        $content .= $key."[] = ".$elem[$i]."\r\n";
                    }
                }
                else if($elem=="") $content .= $key." = \r\n";
                else $content .= $key." = ".$elem."\r\n";
            }
        }
        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        if (!fwrite($handle, $content)) {
            return false;
        }
        fclose($handle);
        return true;
    }

    public function httpsRequest($url, $data=null) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);


        if(!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }
}