<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Mapellm extends Model
{
    public function siswa()
    {
        return $this->belongsToMany('App\Models\Siswa')->using('App\Models\Kelaslm');
    }
}
