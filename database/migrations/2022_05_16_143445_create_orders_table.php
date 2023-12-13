<?php

use App\Models\Order;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained();
            $table->foreignId('affiliate_id')->nullable()->constrained();
            // TODO: Replace floats with the correct data types (very similar to affiliates table)
            // Insight: Using floats is not suitable for representing monetary values due to potential rounding errors and imprecise calculations. For accurate representation, it is recommended to utilize the decimal data type.
            $table->decimal('subtotal', 10,2);
            $table->decimal('commission_owed', 10,2)->default(0.00);
            $table->string('payout_status')->default(Order::STATUS_UNPAID);
            $table->string('discount_code')->nullable();
            $table->string('external_order_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
