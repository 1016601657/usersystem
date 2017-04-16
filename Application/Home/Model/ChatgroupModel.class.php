<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/16
 * Time: 11:26
 * 处理聊天相关数据
 */
namespace Home\Model;

use Think\Model;

class ChatgroupModel extends Model
{
    protected $tableName = 'chatgroup';
    protected $chatgroup;
    protected $groupdetail;

    public function _initialize()
    {
        //初始 实例化聊天群组模型
        $this->chatgroup = M('chatgroup');
        $this->groupdetail = M('groupdetail');
    }

    /**
     * 根据用户id获取其所处的群组
     * @param $uid
     * @return mixed
     */
    public function getUserGroup($uid){
        return $this->groupdetail->field('groupid')->where('userid', $uid)->group('groupid')->select();
    }

    /**
     * 根据群组id获取其信息
     * @param $groupid
     * @return mixed
     */
    public function getGroupInfo($groupid){
        return $this->chatgroup->where('id', $groupid)->find();
    }

    /**
     * 根据群组id获取群主信息
     * @param $groupid
     * @return mixed
     */
    public function getChatOwnerInfo($groupid){
        return $this->chatgroup->field('owner_name,owner_id')->where('id = ' . $groupid)->find();
    }

    /**
     * 根据群组id获取群成员
     * @param $groupid
     * @return mixed
     */
    public function getGroupMembers($groupid){
        return  $this->groupdetail->field('userid as id,username')->where('groupid = ' . $groupid)->select();
    }

    /**
     * 为用户设置一个默认分组
     * @param $data
     * @return mixed
     */
    public function setDefaultGroup($data){
        return $this->groupdetail->data($data)->add();
    }
}