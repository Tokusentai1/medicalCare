<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
