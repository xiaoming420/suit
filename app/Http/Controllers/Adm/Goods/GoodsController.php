<?php

/**
 * 商品管理控制器
 */
namespace App\Http\Controllers\Adm\Goods;

use App\Models\goods;
use App\Models\mcds_banner;
use App\Models\orders;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GoodsController extends Controller
{
    /**
     * 商品列表
     */
    public function goodslist(Request $request)
    {
        $list = goods::orderBy('sorts', 'desc')->orderBy('id', 'desc')->paginate(12);
        foreach ($list as &$v) {
            $start_groups = orders::where('goods_id', $v['id'])->count('id');
            $success = orders::where(['goods_id'=>$v['id'], 'order_status'=>3])->count('id');
            $goods_helps = orders::goodshelps($v['id']);
            $v['start_groups'] = $start_groups;  // 单个商品对应的开团数
            $v['success'] = $success;      // 商品对应的成功数
            $v['goods_helps'] = $goods_helps;      // 商品对应的助力数
        }
        return view('admin/goods/goodslist', ['list'=>$list]);
    }

    /**
     * 添加商品
     */
    public function addgoods(Request $request)
    {
        if ($request->isMethod('post')) {
            $redirect_url = 'addgoods';
            $sku_id = $request->input('sku_id', 0);
            $name = trim($request->input('name', ''));
            $price = trim($request->input('price', ''));
            $stock = (int)$request->stock;
            $description = $request->input('description', '');
            $img_url = $request->file('images');
            $sorts = $request->input('sorts', 1);
            $helps = (int) $request->helps;
            if (empty($sku_id) || empty($name) || empty($price)  || empty($img_url) || empty($helps)) {
                return fun_error_view(0, '缺少参数', $redirect_url);
            }

            $ext = strtolower($img_url->getClientOriginalExtension());     // 扩展名
            if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                return fun_error_view(0, '上传图片格式错误', $redirect_url);
            }
            if (isset($_FILES['images']['size']) && $_FILES['images']['size'] >= 8*1024*1024) {
                return fun_error_view(0, '上传图片大小不得超过8M', $redirect_url);
            }
            $tem_img = 'goods_'.str_random(16) . '.'.$ext;
            $put_result = Storage::disk('goods_images')->put(
                $tem_img,
                file_get_contents($img_url->getRealPath()),
                'public'
            );
            if (!$put_result) {
                return fun_error_view(0, '上传图片失败', $redirect_url);
            }

            $data = [
                'sku_id' => $sku_id,
                'name' => $name,
                'images' => $tem_img,
                'price' => $price,
                'stock' => $stock,
                'stock_use' => $stock,
                'helps' => $helps,
                'sorts' => $sorts,
                'is_valid' => 1,
                'description' => $description,
                'ut' => date('Y-m-d H:i:s'),
                'ct' => date('Y-m-d H:i:s')
            ];

            $res = goods::insert($data);
            if (!$res) {
                return fun_error_view(0, '添加失败', $redirect_url);
            }
            return fun_error_view(1, '添加成功', 'goodslist');
        }
        return view('admin/goods/addgoods');
    }

    /**
     * 修改商品
     * @param Request $request
     */
    public function editgoods(Request $request)
    {
        if ($request->isMethod('post')) {
            $goods_id = (int) $request->input('id');
            $name = trim($request->input('name', ''));
            $price = $request->input('price');
            $stock = (int)$request->input('stock');
            $img_url = $request->file('images');
            $description = $request->input('description', '');
            $sorts = $request->input('sorts', 1);
            $redirect_url = 'editgoods?id='.$goods_id;

            if (empty($goods_id) || empty($name) || empty($price)) {
                return fun_error_view(0, '缺少参数', $redirect_url);
            }
            $goods_info = goods::where(['id'=>$goods_id, 'is_valid'=>1])->first();
            if (!$goods_info) {
                return fun_error_view(0, '未找到该商品', $redirect_url);
            }
            if ($goods_info->stock > $stock) {
                return fun_error_view(0, '商品库存不能减少', $redirect_url);
            }
            $data = []; // 修改数据容器
            if (!empty($img_url)) {
                $ext = strtolower($img_url->getClientOriginalExtension());     // 扩展名
                if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    return fun_error_view(0, '上传图片格式错误', $redirect_url);
                }
                if (isset($_FILES['images']['size']) && $_FILES['images']['size'] >= 8*1024*1024) {
                    return fun_error_view(0, '上传图片大小不得超过8M', $redirect_url);
                }
                $tem_img = 'goods_'.str_random(16) . '.'.$ext;
                $put_result = Storage::disk('goods_images')->put(
                    $tem_img,
                    file_get_contents($img_url->getRealPath()),
                    'public'
                );
                if (!$put_result) {
                    return fun_error_view(0, '上传图片失败', $redirect_url);
                }
                $data['images'] = $tem_img;
            }
            $data['name'] = $name;
            $data['price'] = $price;
            $data['stock'] = $stock;
            $data['stock_use'] = $stock;
            $data['description'] = $description;
            $data['sorts'] = $sorts;
            $data['ut'] = date('Y-m-d H:i:s');
            $res = goods::where(['id'=>$goods_id])->update($data);
            if (!$res) {
                return fun_error_view(0, '修改失败', 'goodslist');
            }
            return fun_error_view(1, '修改成功', 'goodslist');
        }


        $id = (int) $request->id;
        $info = goods::where(['id'=>$id, 'is_valid'=>1])->first();
        if (!$info) {
            return fun_error_view(0, '系统繁忙，请稍后在试', 'goodslist');
        }
        return view('admin/goods/editgoods', ['info'=>$info->toArray()]);
    }

    public function delgoods(Request $request)
    {
        $goods_id = (int) $request->input('id');
        if (empty($goods_id)) fun_respon(0, '缺少参数');
        $res = goods::where(['id'=>$goods_id])->update(['is_valid'=>0, 'ut'=>date('Y-m-d H:i:s')]);
        if ($res) {
            ajax_respon(1, '下架成功');
        }
        ajax_respon(0, '下架失败');
    }
}
