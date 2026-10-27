<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        return response()->json(Category::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'C_id' => 'required|string|size:6|unique:Categories,C_id',
            'C_name' => 'required|string|max:100',
            'C_type' => 'required|string|max:50',
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    public function show(string $id)
    {
        return response()->json(Category::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'C_name' => 'sometimes|required|string|max:100',
            'C_type' => 'sometimes|required|string|max:50',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category berhasil dihapus'
        ]);
    }
}
