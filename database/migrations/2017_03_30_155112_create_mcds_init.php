<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcdsInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 用户表
        if ( !Schema::hasTable('mcds_users') ) {
            Schema::create('mcds_users', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('id')->comment('自增ID');
                $table->string('phone', 30)->default('')->comment('手机号');
                $table->string('nickname', 60)->default('')->comment('昵称');
                $table->string('wb_id', 30)->default('')->comment('微众ID号');
                $table->string('openid', 60)->default('')->comment('openid');
                $table->string('unionid', 60)->default('')->comment('unionid');
                $table->tinyInteger('gender')->default(0)->comment('性别: 0,没有 1男 2女');
                $table->string('province', 100)->default('')->comment('省');
                $table->string('city', 100)->default('')->comment('市/区');
                $table->string('avatar_url',150)->default('')->comment('头像url');
                $table->string('qrcode', 150)->default('')->comment('二维码图片');
                $table->string('woaap_openid', 50)->default('')->comment('中控的openid');
                $table->string('work', 40)->default('')->comment('星座');
                $table->string('address', 120)->default('')->comment('地址');
                $table->tinyInteger('is_valid')->default(1)->comment('有效');
                $table->timestamps();
                $table->unique('openid');
                $table->unique('unionid');
                $table->index('phone');
                $table->index('is_valid');
            });
        }

        // 卡券配置表
        if ( !Schema::hasTable('mcds_card_sys') ) {
            Schema::create('mcds_card_sys', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('id')->comment('卡券配置表主键ID');
                $table->string('card_id', 30)->comment('卡券的ID');
                $table->float('card_point')->comment('卡券所需积分');
                $table->string('start_time', 15)->default('')->comment('上线开始时间');
                $table->string('end_time', 15)->default('')->comment('上线结束时间');
                $table->string('time_section', 200)->default('')->comment('开放时间段  json格式 {"start":"12","end":"14"} 可继续追加字段');
                $table->integer('inventory')->comment('库存总数');
                $table->integer('surplus')->comment('剩余库存份额');
                $table->string('image_url', 80)->comment('卡券的图片地址');
                $table->string('back_img', 60)->default('')->comment('卡券详情背景图片');
                $table->string('list_bakg', 60)->default('')->comment('卡券列表背景图');
                $table->string('inter_img', 60)->comment('卡券列表的背景');
                $table->string('goods_name', 50)->comment('卡券对应的商品名称');
                $table->string('detail', 50)->comment('卡券说明文');
                $table->integer('is_valid')->default(1)->comment('是否删除 0已删除 1未删除');
                $table->unique('card_id');
                $table->index('is_valid');
                $table->index(['surplus', 'is_valid']);
            });
        }


        // 积分订单表
        if ( !Schema::hasTable('mcds_order') ) {
            Schema::create('mcds_order', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('order_id')->comment('自增订单ID');
                $table->string('order_no', 50)->default('')->comment('订单编号');
                $table->integer('user_id')->comment('关联用户ID');
                $table->tinyInteger('order_status')->default(1)->comment('订单状态 1未扣减积分 2已扣减积分 3无效订单');
                $table->string('card_id', 30)->default('')->comment('优惠券卡号');
                $table->float('integral')->default(0)->comment('积分数');
                $table->tinyInteger('is_valid')->default(1)->comment('是否有效，0:无效，1:有效');
                $table->timestamps();
                $table->unique('order_no');
                $table->index('order_status');
                $table->index('user_id');
                $table->index(['user_id', 'order_status']);
                $table->index('card_id');
            });
        }

        // 门店表
        if ( !Schema::hasTable('mcds_store') ) {
            Schema::create('mcds_store', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_general_ci';
                $table->increments('id')->comment('自增ID');
                $table->string('store_name', 60)->comment('门店名称');
                $table->string('location', 155)->comment('地址');
                $table->string('latitude', 15)->comment('经度');
                $table->string('longitude', 15)->comment('纬度');
                $table->string('telephone', 18)->comment('电话');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mcds_users');
        Schema::dropIfExists('mcds_card_sys');
        Schema::dropIfExists('mcds_fix_integral');
        Schema::dropIfExists('mcds_order');
        Schema::dropIfExists('mcds_store');
    }
}
