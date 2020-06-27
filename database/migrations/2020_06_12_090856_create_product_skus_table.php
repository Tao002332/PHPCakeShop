<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_product_sku', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("spu_id");
            $table->decimal("price",10,2);
            $table->integer("stock");
            $table->string("attribute_list");
            $table->tinyInteger("data_flag");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_product_sku');
    }
}
