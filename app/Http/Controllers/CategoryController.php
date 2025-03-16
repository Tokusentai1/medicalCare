<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories()
    {
        $categories = Category::select('name', 'picture')->get()->map(function ($category) {
            $category->picture = url('storage/categoryImages/' . $category->picture);
            return $category;
        });

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $categories
            ]
        );
    }

    public function getCategory(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "There is no Category with this ID " . $id,
                    "result" => null
                ]
            );
        }

        $medicines = $category->medicines()->get()->map(function ($medicine) {
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

    public function searchCategory(string $name)
    {
        $category = Category::where('name', 'like', '%' . $name . '%')->get();

        if ($category->isEmpty()) {
            return response()->json(
                [
                    "success" => false,
                    "statusCode" => 400,
                    "error" => "There is no Category with this name " . $name,
                    "result" => null
                ]
            );
        };

        $category->map(function ($category) {
            $category->picture = url('storage/categoryImages/' . $category->picture);
            return $category;
        });

        return response()->json(
            [
                "success" => true,
                "statusCode" => 200,
                "error" => null,
                "result" => $category
            ]
        );
    }
}
