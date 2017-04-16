<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>用户管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/Public/layui/css/layui.css"  media="all">
    <link rel="stylesheet" href="/Public/css/custom.css">
</head>
<body>
<ul class="layui-nav">
    <li class="layui-nav-item">
        <a href="<?php echo U('Index/index');?>">用户</a>
    </li>
    <li class="layui-nav-item"><a href="<?php echo U('Chat/index');?>">IM聊天</a></li>
    <?php if($Think.session.uid): ?><li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/doLogout');?>" style="color: #A9B7B7;">登出</a></li>
        <li class="layui-nav-item" style="float:right;"><a href="javascript:void(0);" style="color: #A9B7B7;"><?php echo (session('uname')); ?></a></li>
        <?php else: ?>
        <li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/login');?>" style="color: #A9B7B7;">登录</a></li>
        <li class="layui-nav-item" style="float:right;"><a href="<?php echo U('Login/register');?>" style="color: #A9B7B7;">注册</a></li><?php endif; ?>
</ul>

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
    <legend>用户信息</legend>
</fieldset>
<table class="layui-table" lay-even="" lay-skin="row">
    <colgroup>
        <col width="150">
        <col width="150">
        <col width="200">
        <col>
    </colgroup>
    <thead>
    <tr>
        <th>序号</th>
        <th>用户名</th>
        <th>电话</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        <tr class='even pointer user-no-data' <?php if(!empty($users)): ?>style="display:none;"<?php endif; ?> >
            <td colspan="4" style="text-align: center;">暂无数据</td>
        </tr>
    <?php if(is_array($users)): foreach($users as $k=>$item): ?><tr id="user_<?php echo ($item["uid"]); ?>">
            <td><?php echo ($item["uid"]); ?></td>
            <td><?php echo ($item["uname"]); ?></td>
            <td><?php echo ($item["tel_num"]); ?></td>
            <td><a href="<?php echo U('Index/edit','uid='.$item['uid']);?>">修改</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="return deleteUser(<?php echo ($item["uid"]); ?>)">删除</a></td>
        </tr><?php endforeach; endif; ?>
    </tbody>
</table>
<?php echo ($page); ?>
<script src="/Public/js/jquery.min.js" type="text/javascript"></script>
<script src="/Public/layui/layui.js" charset="utf-8"></script>
<script src="/Public/js/custom.js" charset="utf-8"></script>
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
    $(function () {
        var url = window.location;
        if(url == 'http://local.mytest.com/'){
            url = 'http://local.mytest.com/index.php/Home/Index/index.html';
        }
        $('.layui-nav a').filter(function () {
            return (this.href == url);
        }).parent('li').addClass('layui-this');
    });
</script>
</body>
</html>