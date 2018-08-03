<?php
/**
 * @className  ：  公共验证类
 * @description：验证公共调用方法
 * @author     :calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\validate;


class BaseValidate
{
    /**
     * @var array 无需去除空格字段数据
     */
    protected $except = [
        //
    ];

    /**
     * 验证返回
     *
     * @param $validator
     *
     * @return mixed
     */
    public function returnValidate($validator)
    {
        if ($validator->hasErrors()) {
            $base = new \Addons\api\model\BaseModel();
            return $base->returnMessage(2001, '响应失败', $validator->getAllErrors());
        }

        return $validator->getValidData();
    }

    /**
     * @function 去除空格
     * @author   Felix <Fzhengpei@gmail.com>
     *
     * @param $data
     *
     * @return mixed
     */
    public function trimStrings($data)
    {
        foreach ($data as $key => $value) {
            if ( !in_array($key, $this->except, true)) {
                $data[$key] = is_string($value) ? trim($value) : $value;
            }
        }

        return $data;
    }


}