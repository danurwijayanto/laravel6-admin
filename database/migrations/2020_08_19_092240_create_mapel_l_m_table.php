<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapelLMTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapellm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_mapel', 11)->unique();
            $table->string('nama_mapel', 35)->unique();
            $table->unsignedInteger('jumlah_kelas');
            $table->unsignedInteger('kuota_kelas');
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
        Schema::dropIfExists('mapellm');
    }
}
