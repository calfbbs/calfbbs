<?php
/**
 * @className：文件处理类
 * @description：api调用类继承
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace Addons\admin\controller;
use Addons\admin\controller\Base;
class Files extends Base
{

    /**
     * clearAllCache constructor Define a static cache address
     */
    private $cachePathInfo = null;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 调用上传图片api
     */
    public function uploadFile(){
        global $_G;
        $data=$this->post(url('api/files/uploadFile'),array_merge($_POST,$_FILES));
        show_json($data);
    }
    /**
     * 调用删除图片api
     */
    public function deleteFile(){
        global $_G;
        $data=$this->get(url('api/files/deleteFile'),$_GET);
        show_json($data);
    }
    /**
     * 递归调用生成最新路径
     */
    public function dirName()
    {
        if($this->cachePathInfo == null)
        {
            $dirName =  CALFBB.'/data/cache';
            $this->clearAllCache($dirName);

        }else{
            $dirName = $this->cachePathInfo;
            $this->clearAllCache($dirName);

        }
    }

    /**
     * @param 清除缓存Method
     * @var   $this->cachePathInfo | 赋值 -> 调用
     */
    public function clearAllCache($dirName)
    {

        if(is_dir($dirName)){
            if ( $handle = opendir( $dirName ) ) {
                while ( false !== ( $item = readdir( $handle ) ) ) {

                    if ( $item != "." && $item != ".." ) {
                        if (is_dir($dirName."/".$item)) {

                            $this->cachePathInfo = "$dirName/$item";

                             $this->dirName($this->cachePathInfo);

                        } else {

                            if (@unlink($dirName."/".$item));
                        }

                    }

                }
                closedir( $handle );

                if( rmdir( $dirName ) );

            }

        }else{
            $this->error('','缓存目录异常，不是一个文件夹');
        }
    }
}