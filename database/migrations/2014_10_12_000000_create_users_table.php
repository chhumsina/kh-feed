<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar');
            $table->string('password');
            $table->string('phone');
            $table->integer('level');
            $table->string('user_type');
            $table->text('bio');
            $table->boolean('status');
            $table->string('pay_name_1');
            $table->string('pay_number_1');
            $table->string('pay_name_2');
            $table->string('pay_number_2');
            $table->string('pay_name_3');
            $table->string('pay_number_3');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
