<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>助力进行中</title>
</head>
<script type="text/javascript" src="/admin/js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="/admin/js/layer/layer.js"></script>
<body>
<h1 style="text-align: center; margin-top:45%"><?php echo $error; ?></h1>
{{--<a style="display: block;text-align: center; color: #38b1ed;font-size: 24px" herf="http://uc.jianqunbao16.cn/weixin/Share/1af807506fd6edcb33134acd933ef414">链接跳转中</a>--}}
<a style="display: block;text-align: center; color: #38b1ed;font-size: 24px" herf="https://pintuan.missfresh.cn/api/order/groupqrcode">链接跳转中</a>
</body>
<script>
    $(function(){
        alert("<?php echo $error; ?>");

        setTimeout(function(){
//            window.location.href = 'http://uc.jianqunbao16.cn/weixin/Share/1af807506fd6edcb33134acd933ef414';
            window.location.href = 'https://pintuan.missfresh.cn/api/order/groupqrcode';
        }, 1000);
    });
</script>
</html>