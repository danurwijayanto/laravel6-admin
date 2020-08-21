<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nis')->unique();
            $table->string('nama_siswa', 35);
            $table->string('kelas', 30);
            $table->float('nilai_raport', 8, 2);
            $table->unsignedInteger('pilih_lm1');
            $table->unsignedInteger('pilih_lm2');
            $table->unsignedInteger('pilih_lm3');
            $table->float('vektor_v1', 8, 2)->nullable();
            $table->float('vektor_v2', 8, 2)->nullable();
            $table->float('vektor_v3', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('siswa', function ($table) {

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('pilih_lm1')->references('id')->on('mapellm')->onDelete('cascade');
            $table->foreign('pilih_lm2')->references('id')->on('mapellm')->onDelete('cascade');
            $table->foreign('pilih_lm3')->references('id')->on('mapellm')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siswa');
    }
}
