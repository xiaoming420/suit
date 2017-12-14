<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order_helps extends Model
{
    public $table = 'order_helps';
    public $timestamps = false;
    protected $fillable = [];
    public $primaryKey = "id";

    protected function helplist($order_id)
    {
        $res = $this->from('order_helps as oh')
            ->select('u.avatar_url')
            ->leftjoin('users as u', 'u.unionid', '=', 'oh.unionid_help')
            ->where(['oh.order_id'=>$order_id, 'oh.is_valid'=>1])
            ->get()
            ->toArray();
        return $res;
    }
}
