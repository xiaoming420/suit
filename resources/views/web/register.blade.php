<!DOCTYPE HTML>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>用户注册</title>
    <link href="/web/styles/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/web/js/jquery-1.9.1.js"></script>
</head>
<script>
    $(function(){
        $(document).on("click",".sexpro",function(){
            $(this).addClass("active");
            $(this).find("input").attr("checked","checked");
            $(this).siblings().removeClass("active");
            $(this).siblings().find("input").removeAttr("checked");
        });
        $(document).on("click",".checkiconbox",function(){
            if($(this).find(".checkicon").hasClass("active")){
                $(this).find(".checkicon").removeClass("active");
                $(this).find("input").removeAttr("checked");
            }else{
                $(this).find(".checkicon").addClass("active");
                $(this).find("input").attr("checked","checked");
            }
        })
    });
</script>
<body>
<div class="banner"><img src="/web/images/bann002.jpg" /></div>
<div class="contain">
    <p class="itemnav">注册信息</p>
    <div class="iteminfo">
        <!--<input type="text" placeholder="姓名" class="itemput borderbot"/>
        <input type="text" placeholder="性别" class="itemput borderbot"/>
        <input type="text" placeholder="手机号" class="itemput"/>-->
        <div class="itemput borderbot sexput">
            <span class="itempan">姓名</span>
            <div class="itemcont">
                <input type="text" name="user_name" class="itemcontput"/>
            </div>
        </div>
        <div class="itemput borderbot sexput">
            <span class="itempan">性别</span>
            <div class="itemcont">
                <p class="sexpro active"><span class="checkicon02"><input type="radio" name="sex" checked="checked" /></span><span class="">男</span></p>
                <p class="sexpro"><span class="checkicon02"><input type="radio" name="sex"/></span><span class="">女</span></p>
            </div>
        </div>
        <div class="itemput sexput">
            <span class="itempan">手机号</span>
            <div class="itemcont">
                <input type="text" name="phone" class="itemcontput"/>
            </div>
        </div>
    </div>
    <div class="checkiconbox">
        {{--<span class="checkicon active"><input type="checkbox" /></span>--}}
        <p class="">立即注册即可领取硕兰专属<span class="redfont">红包</span></p>
    </div>
    <a class="regbtn">立即注册</a>
</div>
<div class="bottomfixed">
    <p class="notepros">温馨提示：注册完成后会有硕兰专业量体师主动与您联系，请保持手机通常</p>
</div>
</body>


<script>
        $(function(){
            $('#form_data').submit(function(){
                var user_name = $('input[name=account]').val();
                var pass_word = $('input[name=password]').val();
                if(!user_name){
                    layer.msg('请填写登陆账号', {icon: 5});
                    return false;
                }
                if(!pass_word){
                    layer.msg('请填写登陆密码', {icon: 5});
                    return false;
                }
                $.ajax({
                    url : '/channeladm/dologin',
                    type : 'post',
                    dateType : 'json',
                    data : $(this).serialize(),
                    success : function(msg){
                        if(msg.success == 1){
                            window.location.href = '{{url("channeladm/order/home")}}';
                        } else {
                            //alert(msg.error);
                            layer.msg(msg.error, {icon: 5});
                            return false;
                        }
                    },
                    error : function(msg){
                        console.log('error');
                    }
                })
            });
        });
</script>
</html>