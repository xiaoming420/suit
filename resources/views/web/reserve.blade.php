<!DOCTYPE HTML>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>量体预约表</title>
    <link href="/web/styles/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/web/js/jquery-1.9.1.js"></script>
</head>
<script>
    $(function(){
        $(document).on("click",".sexpro",function(){
            $(this).addClass("active");
            $(this).find("input").attr("checked", "checked");
            $(this).siblings().removeClass("active");
            $(this).siblings().find("input").removeAttr("checked");
        });
    });
</script>
<body>
<div class="banner"><img src="/web/images/bann002.jpg" /></div>
<div class="contain containbot75">
    <p class="itemnav">基本信息</p>
    <div class="iteminfo">
        <!---<input type="text" placeholder="姓名" class="itemput borderbot"/>
        <input type="text" placeholder="性别" class="itemput borderbot sexput" disabled="disabled"/>-->
        <div class="itemput borderbot sexput">
            <span class="itempan">姓名</span>
            <div class="itemcont">
                <input type="text" id="name" class="itemcontput"/>
            </div>
        </div>
        <div class="itemput borderbot sexput">
            <span class="itempan">性别</span>
            <div class="itemcont">
                <p class="sexpro active"><span class="checkicon02"><input type="radio" name="sex" value="1" checked="checked"/></span><span class="">男</span></p>
                <p class="sexpro"><span class="checkicon02"><input type="radio" name="sex" value="2" /></span><span class="">女</span></p>
            </div>
        </div>
        <div class="itemput sexput">
            <span class="itempan">手机号</span>
            <div class="itemcont">
                <input type="text" id="phone" class="itemcontput"/>
            </div>
        </div>
    </div>
    <p class="itemnav">量体地址</p>
    <div class="iteminfo">
        <select name="province" id="province">
            <option value="11">北京市</option>
            <option value="12">天津市</option>
            <option value="13">河北省</option>
        </select>
        <select name="city" id="city" style="display: none">
            <option value="11">北京市</option>
            <option value="12">天津市</option>
            <option value="13">河北省</option>
        </select>
        <select name="area" id="area" style="display: none">
            <option value="11">北京市</option>
            <option value="12">天津市</option>
            <option value="13">河北省</option>
        </select>
        {{--<input type="text" placeholder="省市区" class="itemput borderbot addressicon" disabled="disabled"/>--}}
        <input type="text" id="detail" placeholder="请输入详细地址" class="itemput" />
    </div>
    <p class="itemnav">备注</p>
    <div class="iteminfo">
        <textarea placeholder="偏好样式或其他要求" id="cont" class="itemarea"></textarea>
    </div>
</div>
<div class="bottomfixed subtnbox">
    <p class="subtn">免费上门 量身定制</p>
</div>
</body>
<script>
    $(function(){
        $('.subtn').click(function(){
            var cont = $('#cont').html();
            var phone = $('#phone').val();
            var name = $('#name').val();
            var sex = $('input[name=sex]:checked').val();
            var rule = /^1[34578]{1}\d{9}$/;

            if (!name) {
                alert('请留下您的姓名');
                return false;
            }
            if (!rule.test(phone)) {
                alert('请填写正确的手机号');
                return false;
            }


            console.log(cont)
            console.log(phone)
            console.log(name)
            console.log(sex)



        });

        $('#province').change(function(){
            $('#city').style('display', 'block');
        });

        $('#city').change(function(){
            $('#area').style('display', 'block');
        });
    });
</script>
</html>