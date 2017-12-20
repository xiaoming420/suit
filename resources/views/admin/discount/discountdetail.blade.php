@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>注册红包</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">现注册红包金额：{{$info['money']?$info['money']:0}}</blockquote>
        <blockquote class="layui-elem-quote">添加新的红包金额</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/discount/doeditdiscount')}}"  id="form_data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <fieldset class="layui-elem-field">
                <legend>填写信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">金额：</label>
                        <div class="layui-input-block">
                            <input type="number" name="money" placeholder="请填写金额" value="" class="layui-input" >
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
                var money = $('input[name=money]').val();
                if (!money) {
                    layer.msg('金额不能为空', {icon: 5, time:1500});
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

