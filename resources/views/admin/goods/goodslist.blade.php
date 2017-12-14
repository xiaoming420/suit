@extends('admin.index')
@section('content')
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>商品列表</legend>
        </fieldset>
        <form class="layui-form">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <a  class="layui-btn"  href="{{ url('adm/goods/addgoods') }}" >添加商品</a>
                    </div>
                </div>
            </div>

            {{--<div class="layui-form-item">
                <div class="layui-block">
                    <label class="layui-form-label">商品名称：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="account" value="" class="layui-input" placeholder="请输入商品名称">
                    </div>
                    <div class="fl">
                        <button class="layui-btn">查询</button>
                    </div>
                    <div class="fr">
                        <a  class="layui-btn"  href="{{ url('adm/goods/addgoods') }}" >添加商品</a>
                    </div>
                </div>--}}
            </div>
        </form>
        <table class="layui-table tab-ths">
            <thead>
            <tr>
                <th>商品名称</th>
                <th>图片</th>
                <th>库存</th>
                <th>市场价格</th>
                <th>助力数</th>
                <th>权重</th>
                <th>已开团数</th>
                <th>已助力数</th>
                <th>已成功数</th>
                <th>创建时间</th>
                <th>修改时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($list) && $list)
                @foreach($list as $k=>$v)
                    <tr>
                        <td class="text-center">{{$v['name']}}</td>
                        <td class="text-center"><img style="width:100px;height:100px" src="/goods_images/{{$v['images']}}" alt=""></td>
                        <td class="text-center">{{$v['stock']}}</td>
                        <td class="text-center">{{$v['price']}}</td>
                        <td class="text-center">{{$v['helps']}}</td>
                        <td class="text-center">{{$v['sorts']}}</td>
                        <td class="text-center">{{$v['start_groups']}}</td>
                        <td class="text-center">{{$v['goods_helps']}}</td>
                        <td class="text-center">{{$v['success']}}</td>
                        <td class="text-center">{{$v['ct']}}</td>
                        <td class="text-center">{{$v['ut']}}</td>
                        <td class="text-center">{{$v['is_valid']==0?'已下架':'在售'}}</td>
                        <td class="text-center">
                            <button class="layui-btn" onClick='location.href="{{ url('/adm/goods/editgoods?id='.$v['id']) }}"'>编辑</button>
                            @if($v['is_valid']==1)
                                <button class="layui-btn dels" uid="{{$v['id']}}">下架</button>
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
    $('.dels').click(function(){
        var uid = $(this).attr('uid');
        if (confirm('确认删除么？') == false){
            return false;
        }else{
            $.ajax({
                url : '/adm/goods/delgoods?id='+uid,
                type : 'get',
                dateType : 'json',
                success : function(msg){
                    if (msg.result == 1) {
                        layer.msg('删除成功', {'icon':6});
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert('');
                        return false;
                    }
                }
            })
        }
    });
</script>
@endsection
