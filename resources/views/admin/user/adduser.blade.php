@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>用户管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">添加管理员</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/user/doadduser')}}"  id="form_data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <fieldset class="layui-elem-field">
                <legend>填写信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">账号：</label>
                        <div class="layui-input-block">
                            <input type="text" name="phone" placeholder="请填写账号" value="" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">账号密码：</label>
                        <div class="layui-input-block">
                            <input type="password" name="pass" placeholder="账号密码" value="" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">昵称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="nickname" placeholder="用户昵称" value="" class="layui-input" >
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item text-center" >
                <button class="layui-btn">确认添加</button>
            </div>
        </form>
    </div>
    <script>
        $(function () {

            $('#form_data').submit(function(){
                var $phone = $('input[name=phone]').val();
                var $pass = $('input[name=pass]').val();
                var $nickname = $('input[name=nickname]').val();
                if (!$phone) {
                    layer.msg('账号不能为空', {icon: 5, time:1500});
                    return false;
                }
                if (!$pass) {
                    layer.msg('密码不能为空', {icon: 5, time:1500});
                    return false;
                }
                if (!$nickname) {
                    layer.msg('昵称不能为空', {icon: 5, time:1500});
                    return false;
                }
                $preg = /^[0-9A-Za-z\-_\.]{4,16}$/;
                if (!$preg.test($phone)) {
                    layer.msg('账号格式错误，账号只能由4-16位的字母、数字、下划线、中划线、点组成', {icon: 5, time:5000, btn:['明白啦']});
                    return false;
                }
                $preg = /^[0-9A-Za-z\-_\.\!\@\#\%\^\&\*]{6,16}$/;
                if (!$preg.test($pass)) {
                    layer.msg('密码格式错误，账号只能由6-16位的字母、数字、下划线、中划线、点等字符组成', {icon: 5, time:5000, btn:['明白啦']});
                    return false;
                }




                /*$.ajax({
                    url : '/adm/video/doaddbanner',
                    type : 'post',
                    dateType : 'json',
                    data : $('#form_data').serialize(),
                    success : function(msg){
                        layer.msg(msg.msg, {icon: 6});
                        console.log(msg);
                        return false;
                        window.location.href = '{{url("adm/video/bannerlist")}}';
                    },
                    error : function(msg){
                        console.log(msg);
                    }
                });*/

            })
        })

    </script>
@endsection

