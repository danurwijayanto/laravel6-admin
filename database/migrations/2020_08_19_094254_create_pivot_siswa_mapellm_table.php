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
            $table->unsignedInteger('id_siswa');
            $table->unsignedInteger('id_mapellm');
            $table->string('nama_kelas', 10)->unique();
            $table->dateTime('jadwal');
            // $table->unsignedInteger('urutan_pilihan');
            // $table->float('vektor', 8, 2);
            $table->timestamps();
        });

        Schema::table('kelaslm', function ($table) {

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
            $table->foreign('id_mapellm')->references('id')->on('mapellm')->onDelete('cascade');
            
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
