<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarcodeController extends Controller
{
    /**
     * Halaman konfigurasi cetak stiker barcode
     */
    public function index(Request $request): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedProduct = null;
        if ($request->filled('product_id')) {
            $selectedProduct = Product::find($request->product_id);
        }

        $defaultQty = (int) $request->get('qty', 10);

        return view('admin.barcode.index', compact('products', 'selectedProduct', 'defaultQty'));
    }

    /**
     * Tampilan siap cetak (Print Sheet / Thermal Label)
     */
    public function print(Request $request): View
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:200'],
            'format' => ['required', 'in:thermal_40x30,thermal_50x30,a4_grid'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = (int) $request->qty;
        $format = $request->format;
        $storeName = Setting::get('store_name', 'FEEDMART');

        return view('admin.barcode.print', compact('product', 'qty', 'format', 'storeName'));
    }
}
