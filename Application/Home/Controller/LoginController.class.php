<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 22:34
 * 用户登录注册相关
 */
namespace Home\Controller;

use Think\Controller;
use Home\Model\UsersModel;

class LoginController extends Controller
{
    private $rememberPwd = false;
    protected $user;

    public function _initialize()
    {
        $this->user = new UsersModel();
    }

    public function login(){
        $checkRes = $this->checkUserLoginStatus();
        if ($checkRes) {
            $this->redirect('Index/index');
        } else {
            $this->display();
        }
    }
    public function doLogin(){
        if (IS_AJAX && IS_POST) {
            $mobile = I('post.tel_num', '', 'trim');
            $userpass = I('post.password', '', 'trim');
            $isRememberPwd = I('post.rememberPwd');
            $this->rememberPwd = $isRememberPwd === 'true' ? true : false;

            $response = [];

            //判断手机
            if (!checkMobileWithRegex($mobile)) {
                $response['code'] = 404;
                $response['msg'] = '手机号码格式不对';
                $this->ajaxReturn($response);
            }

            //判断密码
            if (empty($userpass)) {
                $response['code'] = 404;
                $response['msg'] = '密码不得为空';
                $this->ajaxReturn($response);
            }

            //查询用户是否存在
            $Users = D("Users");
            $checkResult = $Users->checkUserLogin($mobile, $userpass);
            if ($checkResult === true) {
                $response['code'] = 200;
                $response['msg'] = '登录成功';
                $this->loginCookie();
                $this->ajaxReturn($response);
            } else {
                $response['code'] = 404;
                $response['msg'] = '登录失败';
                $this->ajaxReturn($response);
            }
        } else {
            $response['code'] = 404;
            $response['msg'] = '登录失败';
            $this->ajaxReturn($response);
        }
    }
    public function doRegister(){
        if (IS_AJAX && IS_POST) {
            $uname = I('post.uname', '', 'trim');
            $tel_num = I('post.tel_num', '', 'trim');
            $userpass = I('post.password', '', 'trim');
            $reuserpass = I('post.repassword', '', 'trim');

            $response = [];
            //判断手机
            if (!checkMobileWithRegex($tel_num)) {
                $response['code'] = 404;
                $response['msg'] = '手机号码格式不对';
                $this->ajaxReturn($response);
            }
            //判断姓名
            if (empty($uname)) {
                $response['code'] = 404;
                $response['msg'] = '姓名不得为空';
                $this->ajaxReturn($response);
            }
            //判断密码
            if (empty($userpass) || empty($reuserpass)) {
                $response['code'] = 404;
                $response['msg'] = '密码不得为空';
                $this->ajaxReturn($response);
            }
            if($userpass != $reuserpass){
                $response['code'] = 404;
                $response['msg'] = '两次密码输入不同';
                $this->ajaxReturn($response);
            }
            // 检查用户是否注册
            $userinfo = $this->user->getUserInfoByMobile($mobile);
            if($userinfo){
                $response['code'] = 404;
                $response['msg'] = '该手机号已注册';
                $this->ajaxReturn($response);
            }
            $user['uname'] = $uname;
            $user['tel_num'] = $tel_num;
            $user['password'] = md5($userpass);
            $user['reg_time'] = date('Y-m-d H:i:s',NOW_TIME);
            $registerResult = $this->user->insertUser($user);
            if($registerResult){
                $response['code'] = 200;
                $response['msg'] = '注册成功';
                session('uid', $registerResult);
                session('uname', $uname);
                session('tel_num', $tel_num);
                $this->ajaxReturn($response);
            }else{
                $response['code'] = 404;
                $response['msg'] = '注册失败';
                $this->ajaxReturn($response);
            }
        } else {
            $response['code'] = 404;
            $response['msg'] = '注册失败';
            $this->ajaxReturn($response);
        }
    }
    public function register(){
        $this->display();
    }
    public function doLogout(){
        session('uid', null);
        session('uname', null);
        session('tel_num', null);
        session_destroy();
        cookie(null);
        $this->redirect("Login/login");
    }

    /**
     * 判断用户是否登录
     * @return bool
     */
    protected function checkUserLoginStatus()
    {
        //判断SESSION
        if (session('?uid') && session('uid')) {
            return true;
        } elseif (!empty(cookie("AUTH"))) {//判断COOKIE
            return checkUserLoginStatusByCookie();
        }
        return false;
    }
    /**
     *设置加密COOKIE
     */
    public function loginCookie()
    {
        //需要写入cookie的数据
        $arr = [
            'uid' => session('uid'),
            'uname' => session('uname'),
            'ukey' => md5(session('uid') . session('tel_num')),
            'utime' => time()
        ];

        //json格式化
        $string = json_encode($arr);

        //cookie加密
        $auth = authcode($string, 'ENCODE', C('COOKIE_AUTH_KEY'));

        //判断是否需要记住密码
        //记住密码保存30天:24*60*60*7=604800,默认保存3天:24*60*60*1=86400
        $expires = $this->rememberPwd ? 604800 : 86400;
        cookie('AUTH', $auth, $expires);
    }
}