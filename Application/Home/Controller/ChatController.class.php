<?php
/**
 * Created by PhpStorm.
 * User: LJS
 * Date: 2017/4/15
 * Time: 15:52
 */
namespace Home\Controller;

use Think\Controller;
use Home\Model\UsersModel;
use Home\Model\ChatgroupModel;

class ChatController extends Controller
{
    protected $user;
    protected $chatgroup;

    public function _initialize()
    {
        $this->user = new UsersModel();//实例化用户模型
        $this->chatgroup = new ChatgroupModel();//实例化群组模型
    }

    /**
     * 聊天界面首页
     */
    public function index(){
        //根据当前登录用户获取用户信息
        $uinfo = $this->user->getUserInfoByID(session('uid'),'uid,uname,tel_num,status');
        $this->assign('uinfo',$uinfo);
        $this->display();
    }

    /**
     * im聊天获取用户的好友列表
     */
    public function getList()
    {
        //查询自己的信息
        $mine = $this->user->getUserInfoByID(session('uid'),'uid,uname,tel_num,status');
        //获取可聊天用户
        $other = $this->user->getMyFriends('uid,uname,tel_num,status');
        //查询当前用户的所处的群组
        $groupArr = [];
        $groups = $this->chatgroup->getUserGroup(session('uid'));
        if(!empty($groups)){
            // 取得该用户所有分组的信息
            foreach($groups as $key=>$vo){
                //获取该群组信息
                $ret = $this->chatgroup->getGroupInfo($vo['groupid']);
                $ret['id'] = (int)$ret['id'];
                if(!empty($ret)){
                    $groupArr[] = $ret;
                }
            }
        }
        $online = 0;
        $group = [];  //记录分组信息
        $list = [];  //群组成员信息
        $j = 0;
        $group[0] = [
            'groupname' => '默认分组',
            'id' => 0,
            'online' => 0,
            'list' => []
        ];
        //处理好友信息
        foreach($other as $k=>$v) {
            $list[$j]['username'] = $v['uname'];
            $list[$j]['id'] = $v['uid'];
            $list[$j]['avatar'] = '/Public/images/icon_avatar_default.png';
            if ('online' == $v['status']) {
                $online++;
            }
            $group[0]['online'] = $online;
            $group[0]['list'] = $list;
            $j++;
        }
        unset($other);
        //组合数据返回
        $return = [
            'code' => 0,
            'msg'=> '',
            'data' => [
                'mine' => [
                    'username' => $mine['uname'],
                    'id' => $mine['uid'],
                    'status' => 'online',
                    'avatar' => '/Public/images/icon_avatar_default.png'
                ],
                'friend' => $group,
                'group' => $groupArr
            ],
        ];
        return $this->ajaxReturn($return);
    }

    /**
     * 获取群组的组员信息
     *
     */
    public function getMembers()
    {
        //获取群组id
        $groupid = I('get.id',0,'intval');
        //群主信息
        $owner = $this->chatgroup->getChatOwnerInfo($groupid);
        //群成员信息
        $list = $this->chatgroup->getGroupMembers($groupid);
        //组合数据返回
        $return = [
            'code' => 0,
            'msg' => '',
            'data' => [
                'owner' => [
                    'username' => $owner['owner_name'],
                    'id' => $owner['owner_id'],
                    'owner_id' => $owner['owner_avatar'],
                    'sign' => $owner['owner_sign']
                ],
                'list' => $list
            ]
        ];
        return $this->ajaxReturn($return);
    }

}