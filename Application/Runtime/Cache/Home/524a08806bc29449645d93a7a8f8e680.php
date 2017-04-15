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

<link rel="stylesheet" href="/Public/css/login.css">
<div class="main layui-clear">
    <div class="fly-panel fly-panel-user" pad20="">
        <div class="layui-tab layui-tab-brief" lay-filter="user">
            <ul class="layui-tab-title">
                <li><a href="<?php echo U('Login/login');?>">登入</a></li>
                <li class="layui-this">注册</li>
            </ul>
            <div class="layui-form layui-tab-content" id="LAY_ucm" style="padding: 20px 0;">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form layui-form-pane">
                        <form action="<?php echo U('Login/doRegister');?>" method="post" onsubmit="return false;" id="registerForm">
                            <div class="layui-form-item"><label for="L_email" class="layui-form-label">手机</label>
                                <div class="layui-input-inline"><input type="text" id="tel_num" name="tel_num" required=""
                                                                       lay-verify="required" autocomplete="off"
                                                                       class="layui-input"></div>
                            </div>
                            <div class="layui-form-item"><label for="L_pass" class="layui-form-label">姓名</label>
                                <div class="layui-input-inline"><input type="text" id="uname" name="uname"
                                                                       required="" lay-verify="required"
                                                                       autocomplete="off" class="layui-input"></div>
                            </div>
                            <div class="layui-form-item"><label for="L_pass" class="layui-form-label">密码</label>
                                <div class="layui-input-inline"><input type="password" id="password" name="password"
                                                                       required="" lay-verify="required"
                                                                       autocomplete="off" class="layui-input"></div>
                            </div>
                            <div class="layui-form-item"><label for="L_pass" class="layui-form-label">确认密码</label>
                                <div class="layui-input-inline"><input type="password" id="repassword" name="repassword"
                                                                       required="" lay-verify="required"
                                                                       autocomplete="off" class="layui-input"></div>
                            </div>

                            <div class="layui-form-item">
                                <button class="layui-btn" lay-filter="*" lay-submit="" onclick="doRegister()">立即注册</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script src="/Public/js/jquery.min.js" type="text/javascript"></script>
<script src="/Public/layui/layui.js" charset="utf-8"></script>
<script src="/Public/js/custom.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use('form', function () {
        var $ = layui.jquery, form = layui.form();
        //全选
        form.on('checkbox(allChoose)', function (data) {
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function (index, item) {
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
    });
    function doRegister(){
        var loginUrl = $("#registerForm").attr('action');
        var uname = $("#uname").val();
        var tel_num = $("#tel_num").val();
        var passWord = $("#password").val();
        var repassword = $("#repassword").val();
        
        $.post(loginUrl, {"uname": uname, "tel_num": tel_num, "password": passWord, "repassword": repassword}, function (response) {
            if (response.code == 200) {
                window.location.href = "<?php echo U('Index/index');?>";
            } else {
                layer.msg(response.msg);
            }
        });
    }
</script>
</body>
</html>