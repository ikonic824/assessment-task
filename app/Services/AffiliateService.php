<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method

        // Check if the email is already in use by the merchant's user
        if ($merchant->user->email === $email) {
            throw new AffiliateCreateException('Email is already in use by the merchant');
        }

        // Check if the email is already in use by another affiliate for the same merchant
        if (Affiliate::where('merchant_id', $merchant->id)->whereHas('user', function ($query) use ($email) {
            $query->where('email', $email);
        })->exists()) {
            throw new AffiliateCreateException('Email is already in use by another affiliate for the same merchant');
        }

        // Create a new user for the affiliate
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random(16)),
            'type' => User::TYPE_MERCHANT,
        ]);

        // Create a new discount code for the affiliate
        $discountCode = $this->apiService->createDiscountCode($merchant);

        // Create a new affiliate for the merchant
        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'merchant_id'=>$merchant->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $discountCode['code'],
        ]);

        // Send an email to the affiliate
        Mail::to($affiliate->user)->send(new AffiliateCreated($affiliate));

        return $affiliate;
    }
}
