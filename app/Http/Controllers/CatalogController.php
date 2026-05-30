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

    /**
     * Show Local Agro Commodities Price Dashboard.
     */
    public function agroDashboard()
    {
        $products = Product::where('is_local_product', true)->get();
        
        // Generate mock historical price trends for the past 6 days + today
        $historyDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $historyDays[] = date('d M', strtotime("-{$i} days"));
        }

        $trends = [
            'Cabai Rawit Merah Lokal (Super Pedas)' => [32000, 34000, 33000, 36000, 39000, 41000, 38000],
            'Bawang Merah Brebes Pilihan' => [29000, 30000, 29500, 31000, 33000, 34000, 28000],
            'Tomat Merah Segar Garut' => [10000, 11500, 12000, 13500, 14000, 13000, 12000],
            'Kentang Dieng Super' => [14000, 14500, 15000, 15500, 16000, 16500, 16000],
            'Beras Merah Organik Cianjur' => [17500, 17500, 18000, 18000, 18200, 18500, 18000],
        ];

        return view('catalog.agro_dashboard', compact('products', 'historyDays', 'trends'));
    }
}
