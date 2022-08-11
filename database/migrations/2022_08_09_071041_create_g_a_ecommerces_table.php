<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGAEcommercesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('g_a_ecommerces', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->unique();
            $table->string('order_date');
            $table->double('revenue', 8, 2);
            $table->double('tax', 8, 2);
            $table->double('shipping', 8, 2);
            $table->double('refund_amount', 8, 2);
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('g_a_ecommerces');
    }
}
