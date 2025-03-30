<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Display a listing of all medicines.
     */
    public function index()
    {
        $medicines = Medicine::select('brand_name', 'image', 'price')
            ->get()
            ->map(function ($medicine) {
                $medicine->image = url('storage/images/' . $medicine->image);
                return $medicine;
            });

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $medicines
            ]
        );
    }


    /**
     * Display the specified medicine via medicine id.
     */
    public function show(int $id)
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "There is no Medicine with this ID " . $id,
                    "result" => null
                ]
            );
        }

        $medicine->image = url('storage/images/' . $medicine->image);

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $medicine
            ]
        );
    }

    /**
     * Search the specified medicine via medicine name.
     */
    public function searchMedicine(string $name)
    {
        $medicine = Medicine::where('brand_name', 'like', '%' . $name . '%')->get();

        if ($medicine->isEmpty()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "There is no Medicine with this name " . $name,
                    "result" => null
                ]
            );
        };

        $medicine->map(function ($medicine) {
            $medicine->image = url('storage/images/' . $medicine->image);
            return $medicine;
        });

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $medicine
            ]
        );
    }
}
