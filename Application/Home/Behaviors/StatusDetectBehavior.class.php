<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 18:45
 * 检测用户登录状态
 */
namespace Home\Behaviors;
class StatusDetectBehavior extends \Think\Behavior
{
    //行为执行入口
    public function run(&$param)
    {
        $m = strtolower(MODULE_NAME);
        $c = strtolower(CONTROLLER_NAME);
        $a = strtolower(ACTION_NAME);

        if (!session("?uid") && !checkUserLoginStatusByCookie() && $c != 'login') {
            redirect(U('Login/login'));
        }
        return true;
    }
}