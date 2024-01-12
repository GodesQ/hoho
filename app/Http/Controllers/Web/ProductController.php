<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) {
        return view("admin-page.products.list-product");
    }

    public function create(Request $request) {
        // return view("create");
    }

    public function store(Request $request) {
    
    }

    public function show($id) {
    
    }

    public function edit($id) {
    
    }

    public function update(Request $request, $id) {
    
    }

    public function destroy($id) {
    
    }

}
