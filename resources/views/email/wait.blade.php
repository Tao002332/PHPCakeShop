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
操作成功 ;<span id="time"></span>


<script>

    var time=document.getElementById("time");
    var i=3;
    var timer=setInterval(function () {
        time.innerHTML=i--;
        if(i<0) {
            clearInterval(timer);
            window.location="https://www.baidu.com/";
        }
    },1000)

</script>
</body>
</html>
