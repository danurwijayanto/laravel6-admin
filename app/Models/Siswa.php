<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    // public function mapelLm()
    // {
    //     return $this->belongsToMany('App\Models\MapelLm')->using('App\Models\Kelaslm');
    // }

    public function detailLm1()
    {
        return $this->belongsTo('App\Models\Mapellm', 'pilih_lm1');
    }

    public function detailLm2()
    {
        return $this->belongsTo('App\Models\Mapellm', 'pilih_lm2');
    }

    public function detailLm3()
    {
        return $this->belongsTo('App\Models\Mapellm', 'pilih_lm3');
    }
}
