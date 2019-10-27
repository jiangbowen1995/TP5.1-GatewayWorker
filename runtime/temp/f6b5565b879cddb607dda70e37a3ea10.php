<?php /*a:2:{s:88:"D:\soft\environment\InstallDir\htdocs\tp51\application\api\view\v1\home\login\login.html";i:1571833181;s:90:"D:\soft\environment\InstallDir\htdocs\tp51\application\api\view\v1\home\layout\layout.html";i:1571833679;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Document</title>
    <style>
        .txtCenter{
            text-align:center;
            font-size: 30px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .mark{
            text-align: center;
            margin-top: 30px;
            font-family: "Arial Black";
            font-size: 20px;
            margin-bottom: 20px;
        }
        .span1{
            margin-right: 30px;
        }
        .login{
            text-align: right;
        }
    </style>
</head>
<body>
<div class="txtCenter">
    <?php echo htmlentities(app('config')->get('system_name')); ?>
</div>
<span class="login">
    <span style="text-align: center">
        <?php if(app('session')->get('id')): ?> 您好,<?php echo htmlentities(app('session')->get('uname')); ?>|<a href="/login/logout">退出</a>
     <?php else: ?> <a href="/login/login_view">登陆</a>|<a href="/login/reg_view">注册</a>
     <?php endif; ?>
        <a href="/">返回首页</a>
    </span>

</span>
<hr>

<style>
    .form{
        position:relative;
        top:30px;
        left:40px;
    }
    .reg{
        font-size: 5px;
        color:red;
        /*text-decoration: ;*/
    }

</style>
<div class="form">
    <form action="/login/login" method="post" enctype ="multipart/form-data">
        <?php echo token(); ?>
        <table >
            <tr>
                <td>邮箱:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
            <tr>
                <td>密码:</td>
                <td><input type="text" name="password" value=""><span class="reg"><a href="/login/reg_view">还没有账号?去注册</a></span></td>
            </tr>
        </table>
        <input type="submit" value="提交">
        <input type="reset" value="重置">
    </form>
</div>

</body>
</html>
