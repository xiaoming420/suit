<!DOCTYPE HTML>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>用户注册</title>
    <link href="/web/styles/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/web/js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="/web/js/layer/layer.js"></script>
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
                <p class="sexpro active"><span class="checkicon02"><input type="radio" name="sex" checked="checked" value="1"/></span><span class="">男</span></p>
                <p class="sexpro"><span class="checkicon02"><input type="radio" name="sex" value="2"/></span><span class="">女</span></p>
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
    <a class="regbtn" id="submit">立即注册</a>
</div>
<div class="bottomfixed">
    <p class="notepros">温馨提示：注册完成后会有硕兰专业量体师主动与您联系，请保持手机通常</p>
</div>
</body>


<script>
    var state = {url:'/suit/register'};
    history.replaceState(state,'','/suit/register');
    layer.open({
        type: 1
        ,title: false //不显示标题栏
        ,closeBtn: false
        ,area: '300px;'
        ,shade: 0.8
        ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
        ,btnAlign: 'c'
        ,moveType: 1 //拖拽模式，0或者1
        ,content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;" class="cli"></div>'
        , yes: function (index, layero) {

        }
    });
    $(".cli").click(function() {
        layer.closeAll();
    })

    @if(isset($info['phone']) && !empty($info['phone']))
        setTimeout(function () {
            alert('您已经注册过了！只能领取一次优惠哦！');
            WeixinJSBridge.call('closeWindow');
            // window.location.href = 'http://mp.weixin.qq.com/bizmall/mallshelf?id=&t=mall/list&biz=MzU3NTE0ODkyMg==&shelf_id=3&showwxpaytitle=1#wechat_redirect';
        },1000)
    @endif



    $(document).on("touchend","#submit",function(){
        var phone = $("input[name='phone']").val();
        var user_name = $("input[name='user_name']").val();
        var sex = $("input[name=sex]:checked").val();
        if(!phone){alert('手机号不能为空');return false;}
        if(!(/^1[0-9]{10}$/.test(phone))){alert('手机号格式不正确');return false;}
        $.ajax({
            url : '/api/user/doregister',
            type : 'post',
            data : {phone:phone,user_name:user_name,sex:sex},
            dateType : 'json',
            success : function(msg){
                if (msg.result == 1)
                {   alert('注册成功！可凭手机号到店使用优惠');
                    // WeixinJSBridge.call('closeWindow');
                    window.location.href = 'http://mp.weixin.qq.com/bizmall/mallshelf?id=&t=mall/list&biz=MzU3NTE0ODkyMg==&shelf_id=3&showwxpaytitle=1#wechat_redirect';
                } else {
                    alert(msg.msg);
                }
            },
            error : function(msg){
                console.log(msg);
            }
        });
    });
</script>
</html>