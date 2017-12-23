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
<style type="text/css">
    select {

        /*Chrome和Firefox里面的边框是不一样的，所以复写了一下*/

        /*border: solid 1px #000;*/
        border: none;

        /*很关键：将默认的select选择框样式清除*/

        appearance:none;

        -moz-appearance:none;

        -webkit-appearance:none;

        /*在选择框的最右侧中间显示小箭头图片*/

        background: url("http://ourjs.github.io/static/2015/arrow.png") no-repeat scroll right center transparent;

        /*为下拉小箭头留出一点位置，避免被文字覆盖*/

        padding-right: 14px;
        font-size: 14px
        width:33%

    }
</style>
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
        <select name="province" id="province" style="float:left;margin:5px 3px;font-size: 14px">
            <option value="0">请选择省</option>
            <option value="11">北京市</option>
            <option value="12">天津市</option>
            <option value="13">河北省</option>
        </select>
        <select name="city" id="city" style="display: none;float:left;margin:5px 3px;font-size: 14px">
        </select>
        <select name="area" id="area" style="display: none;float:left;margin:5px 3px;font-size: 14px">
        </select>
        {{--<input type="text" placeholder="省市区" class="itemput borderbot addressicon" disabled="disabled"/>--}}
        <input style="font-size: 14px" type="text" id="detail" placeholder="请输入详细地址" class="itemput" />
    </div>
    <p class="itemnav">备注</p>
    <div class="iteminfo">
        <textarea style="font-size: 14px" placeholder="偏好样式或其他要求" id="cont_bak" class="itemarea"></textarea>
    </div>
</div>
<div class="bottomfixed subtnbox">
    <p class="subtn applysub">免费上门 量身定制</p>
</div>
</body>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>

    $(function(){

        $('.applysub').click(function(){

            var name = $('#name').val();
            var sex = $('input[name=sex]:checked').val();
            var phone = $.trim($('#phone').val());
            var cont = $('#cont_bak').val();
            var province = $('#province :selected').text();
            var city = $('#city :selected').text();
            var area = $('#area :selected').text();
            var detail = $('#detail').val();
            var rule = /^1[34578]{1}\d{9}$/;

            console.log(province)
            console.log(city)
            console.log(cont)

            if (!name) {
                alert('请留下您的姓名');
                return false;
            }
            if (!rule.test(phone)) {
                alert('请填写正确的手机号');
                return false;
            }
            var data = {name:name,sex:sex,phone:phone,remark:cont,province:province,city:city,area:area,address:detail};
            $.ajax({
                url : '/subscribe/suppy',
                type : 'post',
                data : data,
                dataType : 'json',
                beforeSend: function () {
                    // 禁用按钮防止重复提交
                    $('.applysub').text('预约提交中...');
                    $('.subtn').attr('onclick','javascript:void();');//改变提交按钮上的文字并将按钮设置为不可点击
                },
                success : function(msg){
                    console.log(msg)
                    if (msg.result == 1) {
                        alert('我们已经收到您的预约，工作人员会尽快联系您！');
                        WeixinJSBridge.call('closeWindow');
                        /*document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
                            // 通过下面这个API显示右上角按钮
                            //WeixinJSBridge.call('showOptionMenu');
                            WeixinJSBridge.call('closeWindow');
                        });*/
                    }
                },
                complete: function () {
                    $('.applysub').text('免费上门 量身定制...').removeAttr('onclick');

                }
            });



        });

        $('#province').change(function(){
            var num = $(this).val();
            $.ajax({
                url : '/subscribe/getcity',
                type : 'post',
                data : {num:num},
                dataType : 'json',
                success : function(msg){
                    if (msg.result == 1) {
                        $('#city').css('display', 'block').html(msg.data);
                    }
                }
            })
        });


        $('#city').change(function(){
            var num = $(this).val();
            $.ajax({
                url : '/subscribe/getcity',
                type : 'post',
                data : {num:num},
                dataType : 'json',
                success : function(msg){
                    if (msg.result == 1) {
                        $('#area').css('display', 'block').html(msg.data);
                    }
                }
            })
        });
    });
</script>
</html>