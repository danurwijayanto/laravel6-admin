<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    public function mapelLm()
    {
        return $this->belongsToMany('App\Models\MapelLm')->using('App\Models\Kelaslm');
    }
}
