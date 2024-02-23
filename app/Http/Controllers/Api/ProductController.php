<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getMerchantProducts(Request $request, $merchant_id) {
        $products = Product::where('merchant_id', $merchant_id)->get();

        return response([
            'status' => TRUE,
            'products' => $products
        ]);
    }

    public function getProduct(Request $request, $product_id) {
        $product = Product::findOrFail($product_id);

        return response([
            'status' => TRUE,
            'product' => $product
        ]);
    }

    // public function 
}
