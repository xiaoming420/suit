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
                            <button class="layui-btn" onClick='location.href="{{ url('/adm/user/edituser?id='.$v['id']) }}"'>编辑</button>
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
<script>
    $('.dels').click(function(){
        var id = $(this).attr('id');
        if (confirm('确认删除么？') == false){
            return false;
        }else{
            $.ajax({
                url : '/subscribe/del?id='+id,
                type : 'post',
                dateType : 'json',
                success : function(msg){
                    if (msg.result == 1) {
                        layer.msg('删除成功', {'icon':6});
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert(msg.msg);
                        return false;
                    }
                },
                error : function(msg){
                    console.log(msg);
                }
            })
        }
    });
</script>
@endsection
