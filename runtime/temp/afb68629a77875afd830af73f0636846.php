<?php /*a:2:{s:86:"D:\soft\environment\InstallDir\htdocs\tp51\application\api\view\v1\home\login\add.html";i:1571656815;s:90:"D:\soft\environment\InstallDir\htdocs\tp51\application\api\view\v1\home\layout\layout.html";i:1571832932;}*/ ?>
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
        margin-left: 680px;
    }
</style>
<div class="form">
    <form action="/login/reg" method="post" enctype ="multipart/form-data">
        <?php echo token(); ?>
        <table >
            <tr>
                <td>姓名:</td>
                <td><input type="text" name="name" value=""></td>
            </tr>
            <tr>
                <td>年龄:</td>
                <td> <input type="text" name="age" value=""></td>
            </tr>
            <tr>
                <td>密码:</td>
                <td><input type="text" name="password" value=""></td>
            </tr>
            <tr>
                <td>邮箱:</td>
                <td><input type="text" name="email" value=""></td>
            </tr>
            <tr>
                <td>手机号:</td>
                <td><input type="text" name="mobile" value=""></td>
            </tr>
            <tr>
                <td>说明:</td>
                <td><input type="text" name="memo" value=""></td>
            </tr>
            <tr>
                <td>性别:</td>
                <td>
                    <select name="sex" id=""><br><br>
                        <option value="1">男</option>
                        <option value="2">女</option>
                        <option value="3">保密</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>头像:</td>
                <td> <input type="file" name="pic" value=""></td>
            </tr>
        </table>

        <input type="submit" value="提交">
        <input type="reset" value="重置">
    </form>
</div>

</body>
</html>
