<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bus extends Model
{
    //

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

}
