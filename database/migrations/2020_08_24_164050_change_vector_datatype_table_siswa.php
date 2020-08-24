<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVectorDatatypeTableSiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('siswa', function ($table) {
            $table->decimal('vektor_v1', 8, 4)->nullable()->change();
            $table->decimal('vektor_v2', 8, 4)->nullable()->change();
            $table->decimal('vektor_v3', 8, 4)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('siswa', function ($table) {
            $table->float('vektor_v1', 8, 2)->nullable()->change();
            $table->float('vektor_v2', 8, 2)->nullable()->change();
            $table->float('vektor_v3', 8, 2)->nullable()->change();
        });
    }
}
