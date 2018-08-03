<?php

/**
 * @className：广告相关接口管理
 * @description：增加广告，删除广告，编辑广告，查询广告
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\controller;
use Addons\api\model\AdvertisementModel;
use Addons\api\validate\AdvertisementValidate;
class Advertisement extends AdvertisementModel
{

    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();

    }

    /**
     * 添加广告方法
     * @param varchar  $name   广告名称
     * @param bool $cid 绑定分类id
     * @param int $sort 排序
     * @param string $image 广告图片地址
     * @param int $type 广告图片地址
     * @return array $data   响应数据
     */
    public function addAdvertisement(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new AdvertisementValidate();
        $validateResult=$validate->addAdvertisementValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }


        /**
         * 插入数据到数据库
         */
        $result=$this->insertAdvertisement($validateResult);


        if($result){
            return $this->returnMessage(1001,'响应成功',(int)$result);

        }else{
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 更新广告
     * @param id  $id   更新广告id
     * @param varchar  $name   广告名称
     * @param bool $cid 绑定分类id
     * @param int $sort 排序
     * @param string $image 广告图片地址
     * @param int $type 广告图片地址
     * @return array $data   响应数据
     */
    public function changeAdvertisement(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new AdvertisementValidate();
        $validateResult=$validate->changeAdvertisementValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }


        /**
         * 判断更新数据是否存在
         */

        $advertisementOne=$this->findAdvertisement(['id'=>$validateResult['id']],['id','name']);

        /**
         * 插入数据到数据库
         */
        if($advertisementOne){
            $result=$this->updateAdvertisement($validateResult,['id'=>$validateResult['id']]);

            if($result){
                return $this->returnMessage(1001,'响应成功',$result);
            }else{
                return $this->returnMessage(2001,'响应错误',$result);
            }

        }else{
            return $this->returnMessage(2001,'响应错误','没有id为'.$validateResult['id'].'的数据');
        }

    }

    /**
     * 删除广告
     * @param id  $id   更新广告id
     */
    public function delAdvertisement(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new AdvertisementValidate();
        $validateResult=$validate->delAdvertisementValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 判断删除数据是否存在
         */

        $advertisementOne=$this->findAdvertisement(['id'=>$validateResult['id']],['id','name']);


        /**
         * 插入数据到数据库
         */


        if($advertisementOne){
            $result=$this->deleteAdvertisement(['id'=>$validateResult['id']]);


            if($result){
                return $this->returnMessage(1001,'响应成功',$result);
            }else{
                return $this->returnMessage(2001,'响应错误',$result);

            }

        }else{
          return   $this->returnMessage(2001,'响应错误','没有id为'.$validateResult['id'].'的数据');
        }

    }

    /**
     * 获取广告列表
     * @param int $cid 绑定分类id
     * @param int $current_page 当前页
     * @param int $page_size 每页显示数量
     */
    public function getAdvertisementList(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new AdvertisementValidate();

        $validateResult=$validate->getAdvertisementListValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 获取当前页数总条数
         */
        $count=$this->countAdvertisement($validateResult);
        $result="";
        if($count > 0){
            /**
             * 查询广告列表
             */
             $result=$this->selectAdvertisement($validateResult);

        }

        $data['pagination']=$this->getPagination($validateResult['page_size'],$validateResult['current_page'],$count);

        if($result){
            $data['list']=$result;
            return  $this->returnMessage(1001,'响应成功',$data);
        }else{
            $data['list']=[];
            return  $this->returnMessage(2001,'响应错误',$data);

        }
    }

    /**
     * 获取单条广告数据
     * @param int $id 广告数据id
     */
    public function getAdvertisementOne(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new AdvertisementValidate();
        $validateResult=$validate->getAdvertisementOneValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $result=$this->findAdvertisement($validateResult);

        if($result){
            return $this->returnMessage(1001,'响应成功',$result);

        }else{
            return $this->returnMessage(2001,'响应错误',false);
        }
    }



}