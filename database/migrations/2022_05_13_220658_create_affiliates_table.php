<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('merchant_id');
            // TODO: Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
            // Insight: Using floats is not suitable for representing monetary values due to potential rounding errors and imprecise calculations. For accurate representation, it is recommended to utilize the decimal data type.
            $table->decimal('commission_rate', 8, 2);
            $table->string('discount_code');
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
        Schema::dropIfExists('affiliates');
    }
};
