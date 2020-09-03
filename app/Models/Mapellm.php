<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Mapellm extends Model
{
    protected $table = 'mapellm';

    public function siswa()
    {
        return $this->belongsToMany('App\Models\Siswa')->using('App\Models\Kelaslm');
    }

    public function choice1()
    {
        return $this->hasMany('siswa', 'id', 'pilih_lm1');
    }

    public function choice2()
    {
        return $this->hasMany('siswa', 'id', 'pilih_lm2');
    }

    public function choice3()
    {
        return $this->hasMany('siswa', 'id', 'pilih_lm3');
    }
}
