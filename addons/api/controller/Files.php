<?php

/**
 * @className：图片处理类
 * @description：上传图片,删除图片
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */
namespace Addons\api\controller;
use Framework\library\File;
use Addons\api\model\BaseModel;
use Addons\api\validate\FilesValidate;
class Files  extends BaseModel
{
    public function __construct()
    {

        $this->vaildateAppToken();
    }
    /**
     * 上传图片
     */
    public function uploadFile(){
        global $_G;
        /**
         * post 字段参数验证是否符合条件
         */
        $validate=new FilesValidate();
        $FILES=array_merge($this->post,$_FILES);
        $validateResult=$validate->uploadFileValidate($FILES);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $file=new \Framework\library\File();
        $result=$file->file_upload($FILES['file']);

        /**
         * 判断是否需要压缩图片
         */
        if(!empty($FILES['width']) && $result['code']==1 && $result['data']){
            $thumb=$file->file_image_thumb(ATTACHMENT_ROOT . '/'.$result['data'],'',$FILES['width']);
            //如果压缩图片成功 删除原图
            if($thumb['code']==1 && $thumb['data']){
                $file->file_delete($result['data']);
                $result=$thumb;
            }
        }

        if($result['code']==1 && $result['data']){
            return $this->returnMessage(1001,'响应成功',$result['data']);
        }else{
            return $this->returnMessage(2001,'响应错误',$result['data']);
        }
    }

    /**
     * 删除图片
     */
    public function deleteFile(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new FilesValidate();
        $validateResult=$validate->deleteFileValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $file=new \Framework\library\File();
        $result=$file->file_delete($validateResult['path']);

        if($result){
            return $this->returnMessage(1001,'响应成功','删除图片成功');
        }else{
            return $this->returnMessage(2001,'响应错误',['删除图片失败'=>',该图片不存在或没有删除图片权限']);
        }
    }
}