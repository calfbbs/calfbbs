<?php
/**
 * @className：文件处理类
 * @description：api调用类继承
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace app\controller;
use app\controller\Base;
class Files extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 调用上传图片api
     */
    public function uploadFile(){
        global $_G;
        $data=$this->post($_G['APP_URL']."?m=api&c=files&a=uploadFile",array_merge($_POST,$_FILES));
        show_json($data);
    }
    /**
     * 调用删除图片api
     */
    public function deleteFile(){
        global $_G;
        $data=$this->get($_G['APP_URL']."?m=api&c=files&a=deleteFile",$_GET);
        show_json($data);
    }
}