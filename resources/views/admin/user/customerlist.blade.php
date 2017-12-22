@extends('admin.index')
@section('content')
    <style>
        .layui-form-label{width: auto}
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>用户列表</legend>
        </fieldset>
        <div class="clearfix">
            <p class="capitalnav capitalnav02">渠道商列表</p>
            <div class="customerdetail">
                <form class="layui-form" method="get" action="{{url('/adm/user/customerlist')}}">
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
                <th>手机号</th>
                <th>性别</th>
                <th>红包金额</th>
                <th>红包是否使用</th>
                <th>注册时间</th>
                <th>短信下发结果</th>
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
                        <td class="text-center">{{$v['gender']==1?'男':($v['sex']==2)?'女':'未知'}}</td>
                        <td class="text-center">{{$v['discount_money']}}</td>
                        <td class="text-center">{{$v['is_used']==0?'未使用':"已使用"}}</td>
                        <td class="text-center">{{$v['created_at']}}</td>
                        <td class="text-center">{{json_decode($v['content'],true)['reason']}}</td>
                        <td class="text-center">
                            @if($v['is_used']==0)
                                <button class="layui-btn check" id="{{$v['id']}}">编辑红包使用状态</button>
                            @else
                                红包使用时间：{{$v['updated_at']}}
                            @endif
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
                url : '/adm/user/editsign',
                type : 'post',
                dateType : 'json',
                data : {id:id},
                success : function(msg){
                    if (msg.result == 1) {
                        layer.msg('操作成功', {'icon':6});
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
