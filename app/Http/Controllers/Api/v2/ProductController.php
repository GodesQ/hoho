<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        return ProductResource::collection(Product::all());
    }

    public function show(Request $request, $product_id) {
        return ProductResource::make(Product::findOrFail($product_id));
    }

    public function merchantProducts(Request $request, $merchant_id) {
        return ProductResource::collection(Product::where('merchant_id', $merchant_id)->get());
    }
 }
