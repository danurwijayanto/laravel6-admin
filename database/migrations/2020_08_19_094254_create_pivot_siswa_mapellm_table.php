<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotSiswaMapellmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelaslm', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('id_siswa');
            $table->bigInteger('id_mapellm');
            $table->string('nama_kelas', 10)->unique();
            $table->dateTime('jadwal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kelaslm');
    }
}
