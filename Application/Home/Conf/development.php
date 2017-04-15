<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 18:05
 * 开发环境相关配置
 */
return array(
    //'配置项'=>'配置值'
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_NAME' => 'user_system',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_PREFIX' => '',
    // 默认语言
    'LANG_SWITCH_ON' => true,
    'LANG_AUTO_DETECT' => true,
    'DEFAULT_LANG' => 'zh-cn',
    'LANG_LIST' => 'en-us,zh-cn',
    // 默认title
    'defaultTitle' => '用户管理',
    // 表单验证hash
    'TOKEN_ON' => TRUE,
    'TOKEN_NAME' => '__hash__',
    //Cookie
    'COOKIE_EXPIRE' => 86400,//1*24*3600
    'COOKIE_DOMAIN' => '',
    'COOKIE_PATH' => '/',
    'COOKIE_HTTPONLY' => '',     // Cookie的httponly属性 3.2.2新增
    'COOKIE_AUTH_KEY' => '3c86537c2ff3333cb4e0da556545f3bfkkd',//COOKIE加密key
    'SESSION_AUTO_START' => true,
    //默认显示的用户分组
    'user_group' => [
        '0' => '测试组',
    ]
);
?>