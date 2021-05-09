<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableCurrencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_currency', function (Blueprint $table) {
            $table->integer('id');
            $table->unsignedBigInteger('one_rs_coin')->default(0);
            $table->unsignedBigInteger('two_rs_coin')->default(0);
            $table->unsignedBigInteger('five_rs_coin')->default(0);
            $table->unsignedBigInteger('ten_rs_coin')->default(0);
            $table->unsignedBigInteger('one_rs_note')->default(0);
            $table->unsignedBigInteger('two_rs_note')->default(0);
            $table->unsignedBigInteger('five_rs_note')->default(0);
            $table->unsignedBigInteger('ten_rs_note')->default(0);
            $table->unsignedBigInteger('twenty_rs_note')->default(0);
            $table->unsignedBigInteger('fifty_rs_note')->default(0);
            $table->unsignedBigInteger('hundread_rs_note')->default(0);
            $table->unsignedBigInteger('two_hundred_rs_note')->default(0);
            $table->unsignedBigInteger('five_hundred_rs_note')->default(0);
            $table->unsignedBigInteger('two_thousand_rs_note')->default(0);
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('available_currency');
    }
}
