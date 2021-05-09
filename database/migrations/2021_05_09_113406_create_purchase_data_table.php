<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_id');
            $table->string('payable_amt');
            $table->string('submitted_currency_object');
            $table->string('returned_currency_object')->nullable();
            $table->enum('booking_status',['initiated','complete','cancelled']);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_data');
    }
}
