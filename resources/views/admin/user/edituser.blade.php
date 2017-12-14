@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>用户管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">编辑用户</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/user/doedituser')}}"  id="form_data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="id" value="{{$info['id']}}">
            <fieldset class="layui-elem-field">
                <legend>填写信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">账号：</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" placeholder="" value="{{$info['phone']}}" disabled class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">昵称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="nickname" placeholder="" value="{{$info['nickname']}}" class="layui-input" >
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item text-center" >
                <button class="layui-btn">确认修改</button>
            </div>
        </form>
    </div>
    <script>
        $(function () {
            $('#form_data').submit(function(){
                var $title = $('input[name=nickname]').val();
                if (!$title) {
                    layer.msg('昵称不能为空', {icon: 5});
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

