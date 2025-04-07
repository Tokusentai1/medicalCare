<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderNotification;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display user order history via user id.
     */
    public function getOrders(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "success" => false,
                "statusCode" => 404,
                "error" => "User not found",
                "result" => null,
            ]);
        }

        $orders = $user->orders()->orderByDesc('created_at')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'total_price' => $order->cart->total_price,
                'status' => $order->status,
                'date' => $order->created_at,
            ];
        });

        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => $orders,
        ]);
    }

    /**
     * Display the specified order via order id.
     */
    public function getOrder(int $id)
    {
        $order = Order::with('cart.medicines')->find($id);

        if (!$order) {
            return response()->json([
                "success" => false,
                "statusCode" => 404,
                "error" => "Order not found",
                "result" => null,
            ]);
        }

        $orderDetails = [
            'id' => $order->id,
            'status' => $order->status,
            'total_price' => $order->cart->total_price,
            'date' => $order->created_at,
            'medicines' => $order->cart->medicines->map(function ($medicine) {
                return [
                    'medicine_id' => $medicine->id,
                    'name' => $medicine->brand_name,
                    'price' => $medicine->price,
                    'quantity' => $medicine->pivot->quantity,
                ];
            }),
        ];

        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => $orderDetails,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function create(Request $request, $user_id)
    {
        logger()->info('Creating order');

        logger()->info('user id: ' . $user_id);

        $validator = Validator::make($request->all(), [
            'medicines' => 'required',
            'total_price' => 'required',
        ]);

        if ($validator->fails()) {
            logger()->error('Validation errors:', $validator->errors()->toArray());
            return response()->json([
                "success" => false,
                "statusCode" => 400,
                "error" => $validator->errors(),
                "result" => null,
            ]);
        }

        logger()->info('Validation passed');

        logger()->info('now to save to the database');

        $order = Order::create([
            'user_id' => $user_id,
            'status' => 'pending',
            'medicines' => $request->medicines,
            'quantities' => array_fill(0, count($request->medicines), 1),
            'total_price' => $request->total_price,
        ]);

        SendOrderNotification::dispatch();

        return response()->json([
            'message' => 'Order created and stock updated successfully!',
            'order' => $order
        ], 201);
    }
}
