<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<p>尊敬的{{$user->user_name}}，请单击链接重置密码。<a href="http://www.30zwtboot.com:8090/forget?id={{$user->id}}&nickname={{$user->nickname }}">重置密码</a></p>
</body>
</html>
