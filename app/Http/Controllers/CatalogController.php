<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    /**
     * Show catalog page.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        // Eager load category to prevent N+1 query problem
        $query = Product::with(['category']);

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Local product filter
        if ($request->filled('local')) {
            $query->where('is_local_product', true);
        }

        $products = $query->get();

        return view('catalog.index', compact('products', 'categories'));
    }

    /**
     * Show product details.
     */
    public function show($id)
    {
        $product = Product::with(['category'])->findOrFail($id);
        return view('catalog.show', compact('product'));
    }
}
