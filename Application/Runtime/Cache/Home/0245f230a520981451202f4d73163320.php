<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/Public/layui/css/layui.css"  media="all">
    <link rel="stylesheet" href="/Public/css/custom.css">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>

<ul class="layui-nav">
    <li class="layui-nav-item layui-this">
        <a href="<?php echo U('Index/index');?>">用户</a>
    </li>
    <li class="layui-nav-item"><a href="">IM聊天</a></li>
    <?php if($Think.session.uid): ?><li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/doLogout');?>" style="color: #A9B7B7;">登出</a></li>
        <li class="layui-nav-item" style="float:right;"><a href="javascript:void(0);" style="color: #A9B7B7;"><?php echo (session('uname')); ?></a></li>
        <?php else: ?>
        <li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/login');?>" style="color: #A9B7B7;">登录</a></li>
        <li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/register');?>" style="color: #A9B7B7;">注册</a></li><?php endif; ?>
</ul>




<script src="/Public/js/jquery.min.js" type="text/javascript"></script>
<script src="/Public/layui/layui.js" charset="utf-8"></script>
<script src="/Public/js/custom.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use('form', function(){
        var $ = layui.jquery, form = layui.form();
        //全选
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });
</script>
</body>
</html>
<script type="text/javascript">
    //localStorage.clear();
    layui.use('layim', function(layim){
        //基础配置
        layim.config({

            //获取主面板列表信息
            init: {
                url: "<?php echo U('Chat/getList');?>" //接口地址（返回的数据格式见下文）
                ,type: 'get' //默认get，一般可不填
                ,data: {} //额外参数
            },
            brief: false //是否简约模式（默认false，如果只用到在线客服，且不想显示主面板，可以设置 true）
            ,title: '我的LayIM' //主面板最小化后显示的名称
            ,maxLength: 3000 //最长发送的字符长度，默认3000
            ,right: '0px' //默认0px，用于设定主面板右偏移量。该参数可避免遮盖你页面右下角已经的bar。
            ,copyright: true //是否授权，如果通过官网捐赠获得LayIM，此处可填true
        });

        //建立WebSocket通讯
        var socket = new WebSocket('ws://127.0.0.1:7272');

        //连接成功时触发
        socket.onopen = function(){
            // 登录
            var login_data = '{"type":"init","id":"{$uinfo.uid}","username":"<?php echo ($uinfo["uname"]); ?>"}';
            socket.send(login_data);
            console.log(login_data);
            console.log("websocket握手成功!");
        };

        //监听收到的消息
        socket.onmessage = function(res){
            //console.log(res.data);
            var data = eval("("+res.data+")");
            switch(data['message_type']){
                // 服务端ping客户端
                case 'ping':
                    socket.send('{"type":"ping"}');
                    break;
                // 登录 更新用户列表
                case 'init':
                    //console.log(data['id']+"登录成功");
                    //layim.getMessage(res.data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                    break;

                // 离线消息推送
                case 'logMessage':
                    setTimeout(function(){layim.getMessage(data.data)}, 1000);
                    break;
                // 用户退出 更新用户列表
                case 'logout':
                    break;
                //聊天还有不在线
                case 'ctUserOutline':
                    console.log('11111');
                    //layer.msg('好友不在线', {'time' : 1000});
                    break;

            }
        };

        //layim建立就绪
        layim.on('ready', function(res){

            layim.on('sendMessage', function(res){
                //console.log(res);
                // 发送消息
                var mine = JSON.stringify(res.mine);
                var to = JSON.stringify(res.to);
                var login_data = '{"type":"chatMessage","data":{"mine":'+mine+', "to":'+to+'}}';
                socket.send( login_data );
            });
        });

    });
</script>