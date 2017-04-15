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
     *
     */
    public function index()
    {
        // 统计用户数
        $count = $this->user->countUsers();
        $Page = new \Think\Page($count);// 实例化分页类 传入总记录数
        $Page->listRows = 2;
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
            $uid = I('post.uid', 0, 'intval');
            $uname = I('post.uname', '', 'trim');
            $tel_num = I('post.tel_num', '', 'trim');
            if (!$uid || !$uname || !$tel_num) {
                $this->error('信息不完整,请重新修改');
            }
            $data = ['uname' => $uname, 'tel_num' => $tel_num];
            $updateResult = $this->user->updateUserInfoByID($data,$uid);
            if($updateResult === false){
                $this->error('修改失败,请重新修改');
            }else{
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
    public function deleteUser(){
        $uid = I('post.uid', 0, 'intval');
        if (!$uid) {
            $retrunData['code'] = 403;
            $retrunData['msg'] = '刪除失敗';
            $this->ajaxReturn($retrunData);
        }
        // 根据用户id获取用户信息
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