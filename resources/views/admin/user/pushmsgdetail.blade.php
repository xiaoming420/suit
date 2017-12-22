@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>推送消息管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">添加新的推送人</blockquote>
        <form class="layui-form" id="form_data">
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
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @if(isset($list) && $list)
                @foreach($list as $k=>$v)
                    <tr>
                        <td class="text-center">{{$v['id']}}</td>
                        <td class="text-center">{{$v['phone']}}</td>
                        <td class="text-center">{{$v['created_at']}}</td>
                        <td class="text-center">
                            <button class="layui-btn dels" id="{{$v['id']}}">删除</button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <script>

        $('.dels').click(function () {
            var id = $(this).attr('id');
            if (confirm('确认删除么？') == false) {
                return false;
            } else {
                $.ajax({
                    url: '/adm/push/del',
                    type: 'post',
                    dateType: 'json',
                    data: {id: id},
                    success: function (msg) {
                        if (msg.result == 1) {
                            layer.msg('删除成功', {'icon': 6});
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert(msg.msg);
                            return false;
                        }
                    },
                    error: function (msg) {
                        console.log(msg);
                    }
                })
            }
        });

        $(function () {
            $('#form_data').submit(function(){
                var phone = $('input[name=phone]').val();
                if (!phone) {
                    layer.msg('手机号不能为空', {icon: 5, time:1500});
                    return false;
                }
                $.ajax({
                    url : '/adm/push/addpush',
                    type : 'post',
                    dateType : 'json',
                    data : $('#form_data').serialize(),
                    success : function(msg){
                        layer.msg(msg.msg, {icon: 6});
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    },
                    error : function(msg){
                        console.log(msg);
                    }
                });

            })
        })

    </script>
@endsection

