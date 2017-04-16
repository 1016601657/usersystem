<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/16
 * Time: 11:26
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
        $this->chatgroup = M('chatgroup');
        $this->groupdetail = M('groupdetail');
    }
    public function getUserGroup($uid){
        return $this->groupdetail->field('groupid')->where('userid', $uid)->group('groupid')->select();
    }
    public function getGroupInfo($groupid){
        return $this->chatgroup->where('id', $groupid)->find();
    }
    public function getChatOwnerInfo($groupid){
        return $this->chatgroup->field('owner_name,owner_id')->where('id = ' . $groupid)->find();
    }
    public function getGroupMembers($groupid){
        return  $this->groupdetail->field('userid as id,username')->where('groupid = ' . $groupid)->select();
    }

}