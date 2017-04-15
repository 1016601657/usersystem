/**
 * Created by LJS on 2017/4/14.
 */
function deleteUser(uid){
    var uname = $("#user_"+uid).children().eq(1).html();
    // 询问框
    layer.confirm('是否删除'+uname+'？', {
        btn: ['确认','取消'] //按钮
    }, function(){
        $.ajax({
            url: '/index.php/Home/Index/deleteUser',
            data: {'uid':uid},
            type: 'POST',
            datatype: 'json',
            success:function(response){
                if(response.code == 200){
                    $("#user_"+uid).remove();
                    if($('table tbody tr').length <= 1){
                        $(".user-no-data").show();
                    }
                    layer.msg('刪除成功');
                }else{
                    layer.msg('刪除失敗');
                }
            }
        });
    }, function(){
    });
}
function doLogin(){
    var loginUrl = $("#loginForm").attr('action');
    var tel_num = $("#tel_num").val();
    var passWord = $("#password").val();
    var rememberPwd = false;
    if ($("#rememberPwd").is(':checked')) {
        rememberPwd = true;
    }
    $.post(loginUrl, {"tel_num": tel_num, "password": passWord, "rememberPwd": rememberPwd}, function (response) {
        if (response.code == 200) {
            window.location.href = "{:U('Index/index')}";
        } else {
            layer.msg(response.msg);
        }
    });
}