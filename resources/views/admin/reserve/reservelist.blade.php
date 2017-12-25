@extends('admin.index')
@section('content')
<style>
        .layui-form-label{width: auto}
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>预约列表</legend>
        </fieldset>
        <div class="clearfix">
            <div class="customerdetail">
                <form class="layui-form" method="get" action="{{url('/adm/reserve/reserveList')}}">
                    <div class="layui-form-item marbot0">
                        <label class="layui-form-label">手机号搜索：</label>
                        <div class="layui-input-inline" style="width:250px;">
                            <input type="text" name="keyword" placeholder="请输入手机号或姓名" class="layui-input" value="{{$keyword?$keyword:''}}">
                        </div>
                        <button class="layui-btn" type="submit">搜索</button>
                    </div>
                </form>
            </div>
        </div>
        <table class="layui-table tab-ths">
            <thead>
            <tr>
                <th>编号</th>
                <th>姓名</th>
                <th>性别</th>
                <th>手机号</th>
                <th>省</th>
                <th>市</th>
                <th>区</th>
                <th>地址</th>
                <th>客户备注</th>
                <th>是否回访</th>
                <th>回访备注</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($list) && $list)
                @foreach($list as $k=>$v)
                    <tr>
                        <td class="text-center">{{$v['id']}}</td>
                        <td class="text-center">{{$v['name']}}</td>
                        @if($v['gender'] == 1)
                            <td class="text-center">男</td>
                        @elseif($v['gender'] == 2)
                            <td class="text-center">女</td>
                        @else
                            <td class="text-center">未知</td>
                        @endif
                        <td class="text-center">{{$v['phone']}}</td>
                        <td class="text-center">{{$v['province']}}</td>
                        <td class="text-center">{{$v['city']}}</td>
                        <td class="text-center">{{$v['area']}}</td>
                        <td class="text-center">{{$v['address']}}</td>
                        <td class="text-center">{{$v['remark']}}</td>
                        <td class="text-center">{{$v['sign']==1?'已回访':'未回访'}}</td>
                        <td class="text-center">{{$v['feedback']}}</td>
                        <td class="text-center">{{$v['ct']}}</td>
                        <td class="text-center">
                        @if($v['feedback'])
                            <button class="layui-btn edit"  ids="{{$v['id']}}" content="{{$v['feedback']}}">修改回访信息</button>
                        @else
                            <button class="layui-btn edit"  ids="{{$v['id']}}" content="">添加回访记录</button>
                        @endif
                            <button class="layui-btn dels" id="{{$v['id']}}">删除</button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        <div class="text-center layui-box layui-laypage layui-laypage-default my_bill_1">
            {{ $list->render() }}
        </div>
    </div>
    <div id="newplant" style="display:none">
        <div class="winareabox">
            <form class="form-horizontal" id = "ajaxform" role="form" method="post"  action="javascript:;">
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">回访备注</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入内容" class="layui-textarea remarks" name="remarks"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(".edit").click(function() {
            var ids = $(this).attr('ids');
            var content = $(this).attr('content');
            $('.remarks').val(content);
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.open({
                    type: 1
                    , title: '编辑回访记录'
                    , area: '450px;'
                    , content: $('#newplant')
                    ,btn: ['确认', '取消']
                    , btnAlign: 'c' //按钮居中
                    , shade: 0 //不显示遮罩
                    , yes: function (index, layero) {
                        var remarks = $('textarea[name=remarks]').val();
                        if (!remarks) {
                            layer.msg('还未填写备注', {icon: 5});
                            return false;
                        }
                        $.ajax({
                            url : '/subscribe/editsign',
                            type : 'post',
                            data : {id:ids,remarks:remarks},
                            dataType : 'json',
                            success : function(msg){
                                console.log(msg);
                                if (msg.result == 1) {
                                    layer.msg(msg.data, {icon: 6,time:1000});
                                    setTimeout(function(){
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    layer.msg(msg.error, {icon: 5});
                                    return false;
                                }
                            }
                        });
                    }
                });
            });
        })
        $('.dels').click(function () {
            var id = $(this).attr('id');
            if (confirm('确认删除么？') == false) {
                return false;
            } else {
                $.ajax({
                    url: '/subscribe/del',
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
    </script>
@endsection
