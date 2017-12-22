@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>推送消息管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">添加新的推送人</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/push/addpush')}}"  id="form_data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <fieldset class="layui-elem-field">
                <legend>填写信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">手机号：</label>
                        <div class="layui-input-block">
                            <input type="number" name="phone" placeholder="请填写手机号" value="" class="layui-input" >
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item text-center" >
                <button class="layui-btn">确认添加</button>
            </div>
        </form>
    </div>
    <blockquote class="layui-elem-quote">推送人员列表</blockquote>
    <table class="layui-table tab-ths">
        <thead>
        <tr>
            <th>编号</th>
            <th>手机号</th>
            <th>创建时间</th>
        </tr>
        </thead>
        <tbody>
            @if(isset($list) && $list)
                @foreach($list as $k=>$v)
                    <tr>
                        <td class="text-center">{{$v['id']}}</td>
                        <td class="text-center">{{$v['phone']}}</td>
                        <td class="text-center">{{$v['created_at']}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <script>
        $(function () {
            $('#form_data').submit(function(){
                var phone = $('input[name=phone]').val();
                if (!phone) {
                    layer.msg('手机号不能为空', {icon: 5, time:1500});
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

