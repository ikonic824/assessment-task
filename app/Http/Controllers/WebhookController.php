<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {

            $data = $request->all();

            // Call the processOrder method of the OrderService
            $this->orderService->processOrder($data);

            // You can customize the response based on the outcome of the processOrder method
            return response()->json(['message' => 'Order processed successfully'], 200);

    }
}
