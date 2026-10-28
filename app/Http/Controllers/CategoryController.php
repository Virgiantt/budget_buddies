<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where(function ($query) {
            $query->where('Users_U_id', auth()->id())
                ->orWhereNull('Users_U_id');
        })
            ->orderBy('C_name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'C_name' => 'required|string|max:100',
            'C_type' => 'required|in:income,expense',
        ]);

        do {
            $id = strtoupper(Str::random(6));
        } while (Category::where('C_id', $id)->exists());

        $data['C_id'] = $id;
        $data['Users_U_id'] = auth()->id();

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $category = Category::where(function ($query) {
            $query->where('Users_U_id', auth()->id())
                ->orWhereNull('Users_U_id');
        })->findOrFail($id);

        return response()->json($category);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::where('Users_U_id', auth()->id())
            ->find($id);

        if (!$category) {
            return redirect()
                ->route('categories.index')
                ->with(
                    'error',
                    'Kategori bawaan tidak bisa diedit, atau kategori tidak ditemukan.'
                );
        }

        $data = $request->validate([
            'C_name' => 'required|string|max:100',
            'C_type' => 'required|in:income,expense',
        ]);

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $category = Category::where('Users_U_id', auth()->id())
            ->find($id);

        if (!$category) {
            return redirect()
                ->route('categories.index')
                ->with(
                    'error',
                    'Kategori bawaan tidak bisa dihapus, atau kategori tidak ditemukan.'
                );
        }

        if (
            $category->transactions()->exists()
            || $category->budgets()->exists()
        ) {
            return redirect()
                ->route('categories.index')
                ->with(
                    'error',
                    'Kategori tidak bisa dihapus karena masih digunakan oleh transaksi atau anggaran.'
                );
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
