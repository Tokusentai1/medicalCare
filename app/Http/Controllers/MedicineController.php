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
        $medicines = Medicine::with('category:id,name') // eager load only id and name
            ->select('id', 'brand_name', 'image', 'price', 'dosage', 'dosage_form', 'category_id') // don't forget to include category_id
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'Name' => $medicine->brand_name,
                    'image' => url('storage/images/' . $medicine->image),
                    'price' => $medicine->price,
                    'dosage' => $medicine->dosage,
                    'dosageForm' => $medicine->dosage_form,
                    'categoryName' => $medicine->category->name ?? null, // use null-safe in case no category
                ];
            });

        return response()->json([
            "success" => true,
            "statusCode" => 200,
            "error" => null,
            "result" => $medicines
        ]);
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
