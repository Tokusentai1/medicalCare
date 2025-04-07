<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /** 
     * Get all categories
     */
    public function getAllCategories()
    {
        $categories = Category::select('id', 'name')->get();

        return response()->json(
            $categories
        );
    }

    /** 
     * Display category by id
     */
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

        $medicines = $category->medicines()->get();

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
     * Search category by name
     */
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
