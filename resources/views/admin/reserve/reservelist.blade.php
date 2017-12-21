@extends('admin.index')
@section('content')
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>预约列表</legend>
        </fieldset>
        <form class="layui-form">
            <div class="layui-form-item">
            </div>
        </form>
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
                        @if($v['sex'] == 1)
                            <td class="text-center">男</td>
                        @else
                            <td class="text-center">女</td>
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
                            <button class="layui-btn"  id="btn">编辑</button>
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
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="proid">商家名称：</label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" id="shopname"  name="shopname" placeholder="请输入商家名称">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="proid">商家url：</label>
                    <div class="col-sm-7">
                        <input class="form-control" type="text" id="shopurl"  name="shopurl" placeholder="请输入商家url">
                    </div>
                </div>
                <div class="layui-layer-btn layui-layer-btn-c" >
                    <button class="layui-btn layui-layer-btn0" type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $("#btn").click(function() {
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.open({
                    type: 1
                    , title: '编辑回访记录'
                    , area: '450px;'
                    , content: $('#showMessage')
                    , btnAlign: 'c' //按钮居中
                    , shade: 0 //不显示遮罩
                    , yes: function () {
                        layer.closeAll();
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
