@extends('admin.index')
@section('content')
    <style>
    </style>
    <div class="layui-main">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>群二维码管理</legend>
        </fieldset>
        <blockquote class="layui-elem-quote">二维码管理</blockquote>
        <form class="layui-form" method="post" action="{{url('/adm/user/group')}}"  id="form_data"  enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="id" value="">
            <fieldset class="layui-elem-field">
                <legend>上传二维码</legend>
                <div class="layui-field-box">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width:100px">二维码图片：</label>
                        <div class="layui-box layui-upload-button">
                            <input type="file" name="images" class="layui-upload-file" id="banner_one">
                            <span class="layui-upload-icon"><i class="layui-icon"></i>上传图片</span>
                        </div>
                        <span style="color: red">建议：图片文件大小在1M以内，以保证用户浏览时的不会出现加载过慢的情况</span>
                        <div id="content_banner_one" style="margin-left: 15%">
                            @if(isset($list) && !empty($list))
                                <img style='float:left;margin:10px 0px 10px 180px;width:120px' src="/goods_images/{{$list['qrcode_url']}}" alt="">
                            @endif

                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="layui-form-item text-center" >
                <button class="layui-btn">确认修改</button>
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


            $('#form_data').submit(function(){
                var $img_url = $('input[name=images]').val();
                if (!$img_url) {
                    layer.msg('图片不能为空', {icon: 5});
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

