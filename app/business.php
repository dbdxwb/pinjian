<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

    class business extends Model
{
    //
    public function setPicsAttribute($pics)
    {
        if (is_array($pics)) {
            $this->attributes['pics'] = json_encode($pics);
        }
    }

    public function getPicsAttribute($pics)
    {
        return json_decode($pics, true);
    }

    public function setFoodTypeAttribute($foodType)
    {
        if (is_array($foodType)) {
            $this->attributes['food_type'] = json_encode($foodType);
        }
    }

    public function getFoodTypeAttribute($foodType)
    {
        return json_decode($foodType, true);
    }

    public function setServeFacilityAttribute($serveFacility)
    {
        if (is_array($serveFacility)) {
            $this->attributes['serve_facility'] = json_encode($serveFacility);
        }
    }

    public function getServeFacilityAttribute($serveFacility)
    {
        return json_decode($serveFacility, true);
    }

    //与热点管理表关联
//    public function hotSpot()
//    {
//        return $this->hasOne(hot_spot::class);
//    }

}
