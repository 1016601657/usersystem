<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

require_once __DIR__ . '/medoo.php';

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 新建一个类的静态成员，用来保存数据库实例
     */
    public static $db = null;

    /**
     * 进程启动后初始化数据库连接
     */
    public static function onWorkerStart($worker)
    {
        $config = [
            'database_type' => 'mysql',
            'database_name' => 'user_system',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'port' => 3306,
            'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
        ];
        self::$db = new medoo($config);
    }
    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $data) {
        $message = json_decode($data, true);
        $message_type = $message['type'];
        switch($message_type) {
            case 'init':
                // uid
                $uid = $message['id'];
                // 设置session
                $_SESSION = [
                    'uname' => $message['username'],
                    'id'       => $uid,
                ];

                // 将当前链接与uid绑定
                Gateway::bindUid($client_id, $uid);
                // 通知当前客户端初始化
                $init_message = array(
                    'message_type' => 'init',
                    'id'           => $uid,
                );
                Gateway::sendToClient($client_id, json_encode($init_message));

                //查询最近1周有无需要推送的离线信息
                $time = time() - 7 * 3600 * 24;
                $resMsg = self::$db->select('chatlog',"*",["AND"=>['toid'=>$uid,'created[>]'=>$time,'type'=>'friend','needsend'=>1]]);
                var_dump($resMsg);
                if( !empty( $resMsg ) ){

                    foreach( $resMsg as $key=>$vo ){

                        $log_message = [
                            'message_type' => 'logMessage',
                            'data' => [
                                'username' => $vo['fromname'],
                                'id'       => $vo['fromid'],
                                'type'     => 'friend',
                                'content'  => htmlspecialchars( $vo['content'] ),
                                'timestamp'=> $vo['timeline'] * 1000,
                            ]
                        ];

                        Gateway::sendToUid( $uid, json_encode($log_message) );

                        //设置推送状态为已经推送
                        self::$db->update("chatlog", ["needsend"=>0], ["id"=>$vo['id']]);
                    }
                }
                //查询当前的用户是在哪个分组中,将当前的链接加入该分组
                $ret = self::$db->select('groupdetail',"groupid",['userid'=>$uid]);
                if( !empty( $ret ) ){
                    foreach( $ret as $key=>$vo ){
                        Gateway::joinGroup($client_id, $vo['groupid']);  //将登录用户加入群组
                    }
                }
                unset( $ret );
                return;
                break;
            case 'chatMessage':
                // 聊天消息
                $type = $message['data']['to']['type'];
                $to_id = $message['data']['to']['id'];
                $uid = $_SESSION['id'];
                $chat_message = [
                    'message_type' => 'chatMessage',
                    'data' => [
                        'username' => $_SESSION['uname'],
                        'id'       => $type === 'friend' ? $uid : $to_id,
                        'type'     => $type,
                        'content'  => htmlspecialchars($message['data']['mine']['content']),
                        'timestamp'=> time()*1000,
                    ]
                ];
                //聊天记录数组
                $param = [
                    'fromid' => $uid,
                    'toid' => $to_id,
                    'fromname' => $_SESSION['uname'],
                    'content' => htmlspecialchars($message['data']['mine']['content']),
                    'created' => time(),
                    'needsend' => 0
                ];
                switch ($type) {
                    // 私聊
                    case 'friend':
                        // 插入
                        $param['type'] = 'friend';
                        if( empty(Gateway::getClientIdByUid($to_id))){
                            $param['needsend'] = 1;  //用户不在线,标记此消息推送
                        }
                        self::$db->insert("chatlog", $param);
                        return Gateway::sendToUid($to_id, json_encode($chat_message));
                    // 群聊
                    case 'group':
                        echo 'group $to_id='.$to_id;
                        echo 'group $client_id='.$client_id;
                        $param['type'] = 'group';
                        self::$db->insert("chatlog", $param);
                        return Gateway::sendToGroup($to_id, json_encode($chat_message), $client_id);
                }
                return;
                break;
            case 'ping':
                return;
            default:
                echo "unknown message $data" . PHP_EOL;
        }
    }
    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id) {
        $logout_message = [
            'message_type' => 'logout',
            'id'           => $_SESSION['id']
        ];
        Gateway::sendToAll(json_encode($logout_message));
    }
}
