<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\CloudinaryStorage;
use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
// use Spatie\LaravelCloudinary\Facades\Cloudinary;

class ProductImageController extends Controller
{
    //

    public function findAll(Request $request)
    {
        $id = $request->input("id");
        $limit = $request->input("limit");
        $products_id = $request->input("products_id");

        $query = ProductImage::with(['product']);

        if ($id) {
            $product = $query->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data Product Image Berhasil di Ambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Product Image Tidak ada',
                    404
                );
            }
        }

        if ($products_id) {
            $query->where('products_id', 'like', '%' . $products_id . '%');
        }

        $products = $query->paginate($limit ?: 10);

        return ResponseFormatter::success(
            $products,
            'Data Product Image Berhasil di Ambil'
        );
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,pdf|max:512|min:100',

                'products_id' => 'required|exists:products,id',
            ]);

            $file = $request->file('image');

            // Generate a unique filename for the stored file
            $filename = str::random(20) . '.' . $file->getClientOriginalExtension();

            // Determine the storage path based on the file type
            $storagePath = $file->getMimeType() == 'application/pdf' ? 'pdfs' : 'images';

            // Store the file in the public disk
            $filePath = $file->storeAs($storagePath, $filename, 'public');

            $productImage = ProductImage::create([
                'image' => $filePath,
                'products_id' => $request->input('products_id'),
            ]);

            $isNew = $productImage->wasRecentlyCreated;

            return ResponseFormatter::success([
                'productImage' => $productImage,
                'isNew' => $isNew,
            ], 'File berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Error saving file', 500);
        }
    }



public function delete(Request $request, $id)
{
    $product = ProductImage::find($id);

    if (!$product) {
        return ResponseFormatter::error(null, 'Product Image not found', 404);
    }

    $product->delete();

    return ResponseFormatter::success(null, 'Product Image deleted successfully');
}



public function update(Request $request, $id)
{
    try {
        $productImage = ProductImage::find($id);

        if (!$productImage) {
            return ResponseFormatter::error(null, 'Product Image not found', 404);
        }

        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'products_id' => 'exists:products,id',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
         if ($productImage->image) {
                Storage::disk('public')->delete($productImage->image);
            }

             $productImage->update([
                'image' => $imagePath,
                'products_id' => $request->input('products_id', $productImage->products_id),
            ]);
        } else {
             $productImage->update([
                'products_id' => $request->input('products_id', $productImage->products_id),
            ]);
        }

        return ResponseFormatter::success($productImage, 'Product Image updated successfully');
    } catch (\Exception $e) {
        return ResponseFormatter::error($e->getMessage(), 'Error updating product image', 500);
    }
}

}



