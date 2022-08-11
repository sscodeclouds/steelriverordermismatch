<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStickiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stickies', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->unique();
            $table->enum('is_test_order', ['yes', 'no']);
            $table->double('revenue', 8, 2);
            $table->enum('order_status', ['approved', 'refunded', 'declined', 'shipped', 'pending']);
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stickies');
    }
}
