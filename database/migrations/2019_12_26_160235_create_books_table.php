<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('page_id');
            $table->string('name');
            $table->string('photo');
            $table->string('isbn');
            $table->string('author');
            $table->float('price');
            $table->string('currency');
            $table->integer('num_page');
            $table->boolean('is_out_of_stock');
            $table->text('description');
            $table->text('outline');
            $table->string('status');
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
        Schema::dropIfExists('books');
    }
}
