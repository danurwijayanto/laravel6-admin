<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Kelaslm extends Pivot
{
    protected $table = 'kelaslm';

    public function course()
    {
        return $this->belongsTo('App\Models\Mapellm', 'id_mapellm', 'id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Siswa', 'id_siswa', 'id');
    }
}
