<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderNotification;
use App\Models\Cart;
use App\Models\Employee;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Add medicine to cart (or create a new cart if none exists).
     */
    public function addToCart(Request $request)
    {
        $user = User::find($request->user_id);
        $medicine = Medicine::find($request->medicine_id);

        if (!$user) {
            return response()->json(["success" => false, "statusCode" => 404, "error" => "User not found", "result" => null]);
        }
        if (!$medicine) {
            return response()->json(["success" => false, "statusCode" => 404, "error" => "Medicine not found", "result" => null]);
        }

        DB::beginTransaction();

        $cart = $user->carts()->where('active', true)->first();
        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->id, 'total_price' => 0, 'active' => true]);
        }

        $existingMedicine = $cart->medicines()->where('medicine_id', $medicine->id)->first();
        $quantity = $request->quantity;

        if ($existingMedicine) {
            $quantity += $existingMedicine->pivot->quantity;
            $cart->medicines()->updateExistingPivot($medicine->id, ['quantity' => $quantity]);
        } else {
            $cart->medicines()->attach($medicine->id, ['quantity' => $quantity]);
        }

        $cart->total_price = $cart->medicines->sum(fn($m) => $m->pivot->quantity * $m->price);
        $cart->save();

        DB::commit();
        return response()->json(["success" => true, "statusCode" => 201, "error" => null, "result" => 'Medicine added to cart']);
    }

    /**
     * Get the active cart for a user.
     */
    public function getCart($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["success" => false, "statusCode" => 404, "error" => "User not found", "result" => null]);
        }

        $cart = $user->carts()->where('active', true)->first();
        if (!$cart) {
            return response()->json([
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => 'No active cart',
            ]);
        }

        $medicines = $cart->medicines()->get()->map(fn($m) => [
            'medicine id' => $m->id,
            'name' => $m->brand_name,
            'image' => url('storage/images/' . $m->image),
            'quantity' => $m->pivot->quantity,
            'price' => $m->price,
        ]);

        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => [
                "cart items" => $medicines,
                'total price' => $cart->total_price
            ]
        ]);
    }

    /**
     * Remove a medicine from the cart.
     */
    public function removeMedicine(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                "success" => false,
                "statusCode" => 404,
                "error" => "User not found",
                "result" => null
            ]);
        }

        $cart = $user->carts()->where('active', true)->first();
        if (!$cart) {
            return response()->json([
                "success" => false,
                "statusCode" => 404,
                "error" => "No active cart found",
                "result" => null
            ]);
        }

        $medicine = $cart->medicines()->where('medicine_id', $request->medicine_id)->first();
        if (!$medicine) {
            return response()->json([
                "success" => false,
                "statusCode" => 404,
                "error" => "Medicine not found in the cart",
                "result" => null
            ]);
        }

        $medicinePrice = $medicine->pivot->quantity * $medicine->price;

        $cart->medicines()->detach($request->medicine_id);

        $cart->total_price -= $medicinePrice;
        if ($cart->total_price < 0) {
            $cart->total_price = 0;
        }
        $cart->save();

        if ($cart->medicines()->count() == 0) {
            $cart->delete();
            return response()->json([
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => 'Medicine removed, cart deleted'
            ]);
        }

        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => 'Medicine removed from cart',
        ]);
    }

    /**
     * Complete purchase (checkout) and create an order.
     */
    public function checkout(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["success" => false, "statusCode" => 404, "error" => "User not found", "result" => null]);
        }

        $cart = $user->carts()->where('active', true)->first();
        if (!$cart) {
            return response()->json(["success" => false, "statusCode" => 400, "error" => "No active cart", "result" => null]);
        }

        foreach ($cart->medicines as $medicine) {
            if ($medicine->quantity < $medicine->pivot->quantity) {
                return response()->json(["success" => false, "statusCode" => 400, "error" => "Not enough stock for " . $medicine->brand_name, "available" => $medicine->quantity]);
            }
        }

        DB::beginTransaction();
        foreach ($cart->medicines as $medicine) {
            $medicine->quantity -= $medicine->pivot->quantity;
            $medicine->save();
        }
        $cart->update(['active' => false]);
        $user->orders()->create(['cart_id' => $cart->id, 'status' => 'pending']);
        DB::commit();

        SendOrderNotification::dispatch();

        return response()->json(["success" => true, "statusCode" => 201, "error" => null, "result" => 'Order placed successfully']);
    }
}
