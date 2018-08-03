<?php

/**
 * @className：插件接口相关信息
 * @description：检测插件，添加插件，删除插件
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\controller;

use Addons\api\model\ModulesModel;
use Addons\api\validate\ModulesValidate;

class Modules extends ModulesModel
{
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }


    /**
     * 获取插件信息
     * @param string 插件名称
     */
    public function getModules()
    {
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new ModulesValidate();
        $validateResult = $validate->getModulesValidate($this->post);

        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $result = $this->getOne(['dir_name' => $validateResult['dir_name']]);

        if ($result) {
            $data['modules'] = $result;
            return $this->returnMessage(1001,'响应成功',$data);
        } else {
            $data['modules'] = [];
            return $this->returnMessage(2001,'响应错误',$data);
        }
    }

    /** 获取模块列表
     * @return mixed
     */
    public function getModuleLists(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate = new ModulesValidate();
        $validateResult = $validate->getModuleListsValidate($this->get);

        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        /**
         * 获取当前页数总条数
         */
        $count = $this->countModules($validateResult);

        if ($count > 0) {
            /**
             * 查询字段，可变
             */

            $result = $this->getModuleList($validateResult);
        }

        $data['pagination'] = $this->getPagination($validateResult['page_size'], $validateResult['current_page'], $count);
        $data['list'] = empty($result) ? [] : $result;

        return $this->returnMessage(1001, '响应成功', $data);
    }

    public function addModules()
    {
        $validate = new ModulesValidate();
        $validateResult = $validate->addModulesValidate($this->post);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        $insertArr = [
            'name' => $validateResult['name'],
            'author' => $validateResult['author'],
            'descr' => $validateResult['descr'],
            'logo' => $validateResult['logo'],
            'version' => $validateResult['version'],
            'dir_name' => $validateResult['dir_name'],
            'install_time' => time(),
        ];
        $result = $this->insertModules($insertArr);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', (int)$result);
        } else {
            return $this->returnMessage(2001, '响应错误', $result);
        }
    }

    /**
     * 删除插件
     * @param name  $name 插件名称
     */
    public function delModules(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new ModulesValidate();
        $validateResult=$validate->delModulesValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 判断删除数据是否存在
         */

        $modulesOne=$this->findModules(['dir_name'=>$validateResult['dir_name']],['mid','name', 'dir_name']);

        /**
         * 插入数据到数据库
         */


        if($modulesOne){
            $result=$this->delModule(['dir_name'=>$validateResult['dir_name']]);


            if($result){
                return $this->returnMessage(1001,'响应成功',$result);
            }else{
                return $this->returnMessage(2001,'响应错误',$result);

            }

        }else{
            return   $this->returnMessage(2001,'响应错误','没有目录为'.$validateResult['dir_name'].'的数据');
        }
    }



}