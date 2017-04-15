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

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
    <legend>修改用户信息</legend>
</fieldset>
<form class="layui-form" action="<?php echo U('Index/edit');?>" method="post">
    <div class="layui-form-item">
        <label class="layui-form-label">用户名</label>
        <div class="layui-input-block">
            <input type="text" name="uname" value="<?php echo ($userInfo["uname"]); ?>" lay-verify="title" autocomplete="off" placeholder="请输入用户名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号</label>
        <div class="layui-input-block">
            <input type="text" name="tel_num" value="<?php echo ($userInfo["tel_num"]); ?>" lay-verify="title" autocomplete="off" placeholder="请输入手机号" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn">立即提交</button>
        </div>
    </div>

    <input type="hidden" name="uid" value="<?php echo ($_GET['uid']); ?>">
</form>

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