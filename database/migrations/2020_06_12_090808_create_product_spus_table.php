<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_product_spu', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('cate_id');
            $table->string('title');
            $table->string('desc');
            $table->string('keyword');
            $table->string('img');
            $table->decimal('discount',3,2);
            $table->decimal('price',10,2);
            $table->timestamp("pd");
            $table->timestamp("expd");
            $table->tinyInteger("data_flag");
            $table->integer("pv");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_product_spu');
    }
}
