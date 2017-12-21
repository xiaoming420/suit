@extends('admin.index')
@section('content')
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>用户列表</legend>
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
                <th>手机号</th>
                <th>性别</th>
                <th>红包金额</th>
                <th>红包是否使用</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($list) && $list)
                @foreach($list as $k=>$v)
                    <tr>
                        <td class="text-center">{{$v['id']}}</td>
                        <td class="text-center">{{$v['name']?$v['name']:'未绑定'}}</td>
                        <td class="text-center">{{$v['phone']?$v['phone']:'未绑定'}}</td>
                        <td class="text-center">{{$v['sex']==1?'男':'女'}}</td>
                        <td class="text-center">{{$v['discount_money']}}</td>
                        <td class="text-center">{{$v['is_used']==0?'未使用':"已使用"}}</td>
                        <td class="text-center">{{$v['updated_at']}}</td>
                        <td class="text-center">
                            <button class="layui-btn check" id="{{$v['id']}}">编辑红包使用状态</button>
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
    $('.check').click(function(){
        var id = $(this).attr('id');
        if (confirm('确认红包已抵用么？') == false){
            return false;
        }else{
            $.ajax({
                url : '/adm/user/editsign?id='+id,
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
