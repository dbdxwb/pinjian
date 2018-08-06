<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class rest_room extends Model
{
    //

    //便民设施序列化
    public function setServePeopleFacilityAttribute($serve_people_facility)
    {
        if (is_array($serve_people_facility)) {
            $this->attributes['serve_people_facility'] = json_encode($serve_people_facility);
        }
    }
    //便民设施序列化
    public function getServePeopleFacilityAttribute($serve_people_facility)
    {
        return json_decode($serve_people_facility, true);
    }

}
