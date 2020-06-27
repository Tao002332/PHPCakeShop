<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_order', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("order_no");
            $table->integer("user_id");
            $table->tinyInteger("order_status");
            $table->decimal("product_money",10,2);
            $table->tinyInteger("deliver_type");
            $table->string("recevicer",50);
            $table->string("recevicer_address",255);
            $table->string("recevicer_phone",20);
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
        Schema::dropIfExists('tb_order');
    }
}
