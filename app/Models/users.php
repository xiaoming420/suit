<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    public $table = 'users';
    public $timestamps = false;
    protected $fillable = ['id','phone','nickname','openid','unionid','gender','province','city','avatar_url','is_valid','is_check','created_at','updated_at','is_used'];
    public $primaryKey = "id";

    protected function userinfo($unionid)
    {
        $res = $this->select('id','nickname','openid','unionid','gender','avatar_url')
            ->where(['unionid'=>$unionid, 'is_valid'=>1])
            ->first();
        return $res ? $res->toArray() : '';
    }

}
