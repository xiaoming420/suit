@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>商品管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">添加商品</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/goods/addgoods')}}"  id="form_data" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <fieldset class="layui-elem-field">
                <legend>填写信息</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品ID：</label>
                        <div class="layui-input-block">
                            <input type="text" name="sku_id" placeholder="请输入每日优鲜商品sku_id" value="" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" placeholder="请输入商品名称" value="" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">价格：</label>
                        <div class="layui-input-block">
                            <input type="text" name="price" placeholder="请输入商品市场价格名称" value="" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">库存：</label>
                        <div class="layui-input-block">
                            <input type="text" name="stock" placeholder="请输入商品库存" value="" class="layui-input" >
                        </div>
                    </div>
                    {{--<div class="layui-form-item">
                        <label class="layui-form-label">模块：</label>
                        <div class="layui-input-block">
                            <select name="module" id="module">
                                <option value="1">开心通告栏banner</option>
                                <option value="2">麦麦开心跳banner</option>
                                <option value="3">麦麦一起玩banner</option>
                            </select>
                        </div>
                    </div>--}}
                    <div class="layui-form-item">
                        <label class="layui-form-label">商品图片：</label>
                        <div class="layui-box layui-upload-button">
                            <input type="file" name="images" class="layui-upload-file" id="banner_one">
                            <span class="layui-upload-icon"><i class="layui-icon"></i>上传图片</span>
                        </div>
                        <span style="color: red">建议：图片文件大小在1M以内，以保证用户浏览时的不会出现加载过慢的情况</span>
                        <div id="content_banner_one" style="margin-left: 15%"></div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">商品描述：</label>
                        <div class="layui-input-block">
                            <input type="text" name="description" placeholder="例如：大力水手爱吃的能量菜" value="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">助力人数：</label>
                        <div class="layui-input-inline">
                            <input type="number" min=1 name="helps" placeholder="领取所需助力人数" value="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">权重：</label>
                        <div class="layui-input-inline">
                            <input type="number" min=1 name="sorts" placeholder="权重越大越靠前" value="" class="layui-input">
                        </div>
                    </div>
                </div>



            </fieldset>
            <div class="layui-form-item text-center" >
                {{--<div class="layui-input-block">--}}
                <button class="layui-btn">确认添加</button>
                {{--</div>--}}
            </div>
        </form>
    </div>
    <script>
        $(function () {

            $('#banner_one').change(function(){
                var inputElement = document.getElementById('banner_one');
                var fileList = this.files;
                var reader = new FileReader();
                reader.readAsDataURL(fileList[0]);
                reader.onload = function(e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload=function(){
                        console.log(image.width);
                        console.log(image.height);
                        var bili = image.width/image.height;
                        var www = 120/bili;
                        $('#content_banner_one').html("<img style='float:left;margin:10px 0px 10px 180px;' src='"+image.src+"' width='120px' height='"+www+"px'/>");
                    }
                };
            });

            function check_content(content, errors)
            {
                if (!content) {
                    layer.msg(errors+'不能为空', {icon: 5});
                    return false;
                }
            }


            $('#form_data').submit(function(){
                var $sku_id = $('input[name=sku_id]').val();
                var $name = $('input[name=name]').val();
                var $stock = $('input[name=stock]').val();
                var $price = $('input[name=price]').val();
                var $img_url = $('input[name=images]').val();
                var $helps = $('input[name=helps]').val();

                if (!$sku_id) {
                    layer.msg('商品sku_id不能为空', {icon: 5});
                    return false;
                }
                if (!$name) {
                    layer.msg('商品名称不能为空', {icon: 5});
                    return false;
                }
                if (!$stock) {
                    layer.msg('商品库存不能为空', {icon: 5});
                    return false;
                }
                if (!$price) {
                    layer.msg('商品市场价格不能为空', {icon: 5});
                    return false;
                }
                if (!$img_url) {
                    layer.msg('图片不能为空', {icon: 5});
                    return false;
                }
                if (!$helps) {
                    layer.msg('助力数不能为空', {icon: 5});
                    return false;
                }
                /*layer.msg('图片上传中，请不要刷新页面 <br>上传成功会自动跳转到列表页...', {
                    time: 3000, //多少秒后自动关闭
                    btn: ['明白了']
                });*/
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

            });
        });

    </script>
@endsection

