<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_product_cates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('pid');
            $table->tinyInteger('ord');
            $table->string('title',50);
            $table->tinyInteger('data_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_product_cates');
    }
}
