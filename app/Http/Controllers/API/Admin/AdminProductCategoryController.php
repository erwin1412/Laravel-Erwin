<?php

namespace App\Http\Controllers\API\Admin;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProductCategoryController extends Controller
{
    //
    protected $user;
    protected $userRoles;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->userRoles = $this->user->roles;

            if ($this->userRoles === "USER") {
                return ResponseFormatter::error(
                    null,
                    'Unauthorized',
                    401
                );
            }

            return $next($request);
        });
    }

    public function findAll(Request $request)
    {
        $id = $request->input("id");
        $name = $request->input("name");
        $limit = $request->input("limit");

        $product = ProductCategory::query();
        if ($id) {
            $product = $product->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data Product Category Berhasil di Ambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Product Category Tidak ada',
                    404
                );
            }
        }

        if ($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }

        $products = $product->paginate($limit ?: 10);

        return ResponseFormatter::success(
            $products,
            'Data Product Category Berhasil di Ambil'
        );
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);

            $product = ProductCategory::create([
                'name' => $request->input('name'),
            ]);

            return ResponseFormatter::success($product, 'Product Category berhasil disimpan');
        } catch (\Exception $e) {
            return ResponseFormatter::error($e, 'Error saving product', 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $product = ProductCategory::find($id);

        if (!$product) {
            return ResponseFormatter::error(null, 'Product Category not found', 404);
        }

        $product->delete();

        return ResponseFormatter::success(null, 'Product Category deleted successfully');
    }

    public function update(Request $request, $id)
    {

        $output =  ($request->input('name'));
        $request->validate([
            'name' => 'string',

        ]);

        $product = ProductCategory::find($id);

        if (!$product) {
            return ResponseFormatter::error(null, 'Product Category not found', 404);
        }

        $product->update([
            'name' => $request->input('name', $product->name),
        ]);
        return ResponseFormatter::success($output, 'Product Category updated successfully');
    }
}
