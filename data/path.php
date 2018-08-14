<?php
/**
 * url 自定义路由配置
 * m 模块名
 * c 控制器名
 * a 方法名
 * paramete 接收参数
 */

return array(
    'user-login'=>
        ['m'=>'app', 'c'=>'index', 'a'=>'index', 'paramete'=>'{index}-{index1}'],
    'user-register'=>
        ['m'=>'app', 'c'=>'index', 'a'=>'test', 'paramete'=>'{index2}-{index3}'],

);