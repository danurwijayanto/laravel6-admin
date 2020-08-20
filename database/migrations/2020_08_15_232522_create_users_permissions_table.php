<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // public function up()
    // {
        // Schema::create('users_permissions', function (Blueprint $table) {
        //     // $table->bigIncrements('id');
        //     $table->unsignedInteger('user_id');
        //     $table->unsignedInteger('permission_id');

        //     // Foreign key constraint
        //     $table->foreign('user_id')->reference('id')->on('users')->onDelete('cascade');
        //     $table->foreign('permission_id')->reference('id')->on('permission')->onDelete('cascade');

        //     //SETTING THE COMPOSITE KEYS
        //     $table->primary(['user_id', 'role_id']);
        //     $table->timestamps();

        //     $table->timestamps();
        // });
    // }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    // public function down()
    // {
    //     Schema::dropIfExists('users_permissions');
    // }
}
