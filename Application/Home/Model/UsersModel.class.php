<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/14
 * Time: 18:11
 * 用户信息表
 */

namespace Home\Model;

use Think\Model;

class UsersModel extends Model
{
    protected $tableName = 'users';
    protected $users;

    public function _initialize()
    {
        $this->users = M('users');
    }

    /**
     * 根据条件获取用户总数
     * @return mixed
     */
    public function countUsers()
    {
        return $this->users->where(['is_delete'=>0])->count();
    }

    /**
     * 根据条件获取用户列表
     * @param $page
     * @param int $limit
     * @param string $field
     * @return mixed
     */
    public function getUsers($page, $limit = 2, $field = 'uid,uname,tel_num')
    {
        $res = $this->users->where(['is_delete'=>0])->page($page, $limit)->order('reg_time DESC')->field($field)->select();
        return $res;
    }
    /**
     * 根据id获取用户信息
     * @param int $uid 用户id
     * @param string $field 查询字段
     * @return mixed
     */
    public function getUserInfoByID($uid, $field = 'uid,uname,tel_num')
    {
        $res = $this->users->where(['is_delete'=>0,'uid'=>$uid])->field($field)->find();
        return $res;
    }
    /**
     * 根据手机号获取用户信息
     * @param int $mobile 用户手机号
     * @param string $field 查询字段
     * @return mixed
     */
    public function getUserInfoByMobile($mobile, $field = 'uid,uname,tel_num')
    {
        $res = $this->users->where(['is_delete'=>0,'tel_num'=>$mobile])->field($field)->find();
        return $res;
    }
    /**
     * 注册用户
     * @param array $user 用户信息
     * @return int
     */
    public function insertUser($user){
        $res = $this->users->data($user)->add();
        return $res;
    }
    /**
     * 根据id修改用户信息
     * @param $data 用戶信息
     * @param $uid  用户id
     * @return mixed
     */
    public function updateUserInfoByID($data, $uid)
    {
        $res = $this->users->where(['uid'=>$uid])->data($data)->save();
        return $res;
    }
    /**
     * 根据id刪除用户信息
     * @param $uid 用户id
     * @return mixed
     */
    public function deleteUserByID($uid)
    {
        $res = $this->users->where(['uid'=>$uid])->setField('is_delete',1);
        return $res;
    }
    public function checkUserLogin($mobile, $password)
    {
        $userInfo = $this->getUserInfoByMobile($mobile,'uid,tel_num,password,uname');
        //判断用户信息是否存在
        if (empty($userInfo)) {
            return -1;
        }

        //密码不正确
        if (strcmp($userInfo['password'], md5($password)) !== 0) {
            return -2;
        }
        //保存session
        session('uid', $userInfo['uid']);
        session('uname', $userInfo['uname']);
        session('tel_num', $userInfo['tel_num']);
        return true;
    }

}