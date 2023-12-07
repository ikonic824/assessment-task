<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
         
        $order = Order::where('external_order_id', $data['order_id'])->first();

        // Check if the order with the given order_id already exists
        if ($order) {
            return; // Ignore duplicate orders
        }

        $affiliate = Affiliate::where('discount_code', $data['discount_code'])->first();

        // If no affiliate found, create a new one
        if (!$affiliate) {
            $affiliate = Affiliate::create([
                'merchant_id' => Merchant::where('domain', $data['merchant_domain'])->value('id'),
                'user_id' => User::where('email', $data['customer_email'])->value('id'),
                'discount_code' => $data['discount_code'],
                'commission_rate' => 0.1,
            ]);
        }

        // Register the affiliate
        $this->affiliateService->register($affiliate->merchant, $data['customer_email'], $data['customer_name'], 0.1);

        // Create the order
        Order::create([
            'subtotal' => $data['subtotal_price'],
            'affiliate_id' => $affiliate->id,
            'merchant_id' => $affiliate->merchant_id,
            'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
            'external_order_id' => $data['order_id'],
            'payout_status' => Order::STATUS_UNPAID,
        ]);
    }
}
