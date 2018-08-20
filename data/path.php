<?php
/**
 * url 自定义路由配置
 * m 模块名
 * c 控制器名
 * a 方法名
 * paramete 接收参数
 */

return array(
    'detail'=>
        ['m'=>'app', 'c'=>'detail', 'a'=>'index', 'paramete'=>'{id}'],
    'user-home'=>
        ['m'=>'app', 'c'=>'users', 'a'=>'home', 'paramete'=>'{uid}'],
    'user-index'=>
        ['m'=>'app', 'c'=>'users', 'a'=>'index', 'paramete'=>'{uid}'],
    'user-set'=>
        ['m'=>'app', 'c'=>'users', 'a'=>'set', 'paramete'=>'{uid}'],
    'user-message'=>
        ['m'=>'app', 'c'=>'users', 'a'=>'message', 'paramete'=>'{uid}'],
    'loginout'=>
        ['m'=>'app', 'c'=>'login', 'a'=>'loginout', 'paramete'=>''],
    'search'=>
        ['m'=>'app', 'c'=>'search', 'a'=>'search', 'paramete'=>'{status}-{orderBy}-{cid}'],
    'search-cid'=>
        ['m'=>'app', 'c'=>'search', 'a'=>'search', 'paramete'=>'{cid}'],
    'sear'=>
        ['m'=>'app', 'c'=>'search', 'a'=>'search', 'paramete'=>'{status-cid}'],
    'page'=>
        ['m'=>'app', 'c'=>'search', 'a'=>'search', 'paramete'=>'{current_page}-{page_size}-{status}-{orderBy}-{cid}'],
    'keyword'=>
        ['m'=>'app', 'c'=>'search', 'a'=>'search', 'paramete'=>'{keyword}'],
    'login'=>
        ['m'=>'app', 'c'=>'login', 'a'=>'index', 'paramete'=>''],
    'signup'=>
        ['m'=>'app', 'c'=>'login', 'a'=>'signup', 'paramete'=>''],
    'forget'=>
        ['m'=>'app', 'c'=>'login', 'a'=>'forget', 'paramete'=>''],
    'forget_mobile'=>
        ['m'=>'app', 'c'=>'login', 'a'=>'forget_mobile', 'paramete'=>''],
    'post-add'=>
        ['m'=>'app', 'c'=>'post', 'a'=>'add', 'paramete'=>''],
    'post-edit'=>
        ['m'=>'app', 'c'=>'post', 'a'=>'edit', 'paramete'=>'{id}'],

);