<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    public function findAll(Request $request)
    {
        $id = $request->input("id");
        $name = $request->input("name");
        $limit = $request->input("limit");
        $price = $request->input("price");
        $description = $request->input("description");
        $tags = $request->input("tags");
        $categories = $request->input("categories_id");
        $price_to = $request->input("price_to");
        $price_from = $request->input("price_from");

        $product = Product::with(['category']);

        if ($id) {
            $product = $product->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data Product Berhasil di Ambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Product Tidak ada',
                    404
                );
            }
        }

        if ($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        if ($limit) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        if($price){
            $product->where('price' , 'like' , '%'. $price .'%');
        }
        if($description){
            $product->where('description' , 'like' , '%'. $description .'%');
        }
        if($tags){
            $product->where('tags' , 'like' , '%'. $tags .'%');
        }
        if($categories){
            $product->where('categories' , 'like' , '%'. $categories .'%');
        }
        if ($price_to) {
            $product->where('price', '<=', $price_to);
        }

        if ($price_from) {
            $product->where('price', '>=', $price_from);
        }

        if($categories){
            $product->where('categories'. $categories);
        }

        $products = $product->paginate($limit ?: 10);

        return ResponseFormatter::success(
            $products,
            'Data Product Berhasil di Ambil'
        );
    }


public function store(Request $request)
{
    try {

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'categories_id' => 'required|exists:product_categories,id',
         ]);

        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'tags' => $request->input('tags'),
            'categories_id' => $request->input('categories_id'),
        ]);

        return ResponseFormatter::success($product, 'Product berhasil disimpan $userRoles');
    } catch (\Exception $e) {

        return ResponseFormatter::error($e->getMessage(), 'Error saving product', 500);
    }
}


public function delete(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return ResponseFormatter::error(null, 'Product not found', 404);
    }

    $product->delete();

    return ResponseFormatter::success(null, 'Product deleted successfully');
}



    public function update(Request $request, $id)
    {

        $output =  ($request->input('name'));
        $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'description' => 'string',
            'tags' => 'nullable|string',
            'categories_id' => 'exists:categories,id',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return ResponseFormatter::error(null, 'Product not found', 404);
        }

        $product->update([
            'name' => $request->input('name', $product->name),
            'price' => $request->input('price', $product->price),
            'description' => $request->input('description', $product->description),
            'tags' => $request->input('tags', $product->tags),
            'categories_id' => $request->input('categories_id', $product->categories_id),
        ]);
        return ResponseFormatter::success($output, 'Product updated successfully');
    }

}


