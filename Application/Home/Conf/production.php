<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 18:05
 * 生产环境相关配置
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
    'defaultTitle' => '找纱网-个人中心',
    // 表单验证hash
    'TOKEN_ON' => TRUE,
    'TOKEN_NAME' => '__hash__',
);
?>