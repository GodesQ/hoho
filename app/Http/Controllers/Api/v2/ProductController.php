<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        return Product::all();
    }

    public function show(Request $request, $product_id) {
        return Product::findOrFail($product_id);
    }

    public function merchantProducts(Request $request, $merchant_id) {
        $products = Product::where('merchant_id', $merchant_id)->get();
    }
 }
