<?php
namespace Home\Controller;

use Think\Controller;
use Home\Model\UsersModel;

class IndexController extends Controller
{
    protected $user;
    protected $p;

    public function _initialize()
    {
        $this->user = new UsersModel();
        $this->p = I('get.p',0,'intval');
    }

    /**
     * 首页-用户管理
     */
    public function index()
    {
        // 统计用户数
        $count = $this->user->countUsers();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 2;
        // 获取用户列表
        $users = $this->user->getUsers($this->p,$Page->listRows);
        $this->assign('users', $users);
        $this->assign('page', $Page->show(true));
        $this->display();
    }

    /**
     * 修改用户信息
     */
    public function edit()
    {
        if (IS_POST) {
            //更新用户信息
            $uid = I('post.uid', 0, 'intval');//用户id
            $uname = I('post.uname', '', 'trim');//用户姓名
            $tel_num = I('post.tel_num', '', 'trim');//用户电话
            if (!$uid || !$uname || !$tel_num) {
                $this->error('信息不完整,请重新修改');
            }
            //需要更新的字段
            $data = ['uname' => $uname, 'tel_num' => $tel_num];
            //根据用户id更新用户信息
            $updateResult = $this->user->updateUserInfoByID($data,$uid);
            //判断修改结果
            if($updateResult === false){
                $this->error('修改失败,请重新修改');
            }else{
                //修改成功后跳转至首页
                redirect(U('Index/index'));
            }
        } else {
            // 获取用户信息
            // 获取用户id
            $uid = I('get.uid', 0, 'intval');
            if (!$uid) {
                $this->error('用户不存在');
            }
            // 根据用户id获取用户信息
            $userInfo = $this->user->getUserInfoByID($uid);
            $this->assign('userInfo', $userInfo);
            $this->display();
        }
    }

    /**
     * 根据用户id删除用户
     */
    public function deleteUser(){
        //获取用户id
        $uid = I('post.uid', 0, 'intval');
        if (!$uid) {
            $retrunData['code'] = 403;
            $retrunData['msg'] = '刪除失敗';
            $this->ajaxReturn($retrunData);
        }
        // 根据用户id删除用户
        $deleteResult = $this->user->deleteUserByID($uid);
        if($deleteResult === false){
            $retrunData['code'] = 403;
            $retrunData['msg'] = '刪除失敗';
        }else{
            $retrunData['code'] = 200;
            $retrunData['msg'] = '刪除成功';
        }
        $this->ajaxReturn($retrunData);
    }

}