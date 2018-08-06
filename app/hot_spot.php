<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hot_spot extends Model
{
    //图片序列化
    public function setPicsAttribute($pics)
    {
        if (is_array($pics)) {
            $this->attributes['pics'] = json_encode($pics);
        }
    }
    //图片序列化
    public function getPicsAttribute($pics)
    {
        return json_decode($pics, true);
    }

    //与商家管理表关联
//    public function business()
//    {
//        return $this->hasOne(business::class);
//    }
}
