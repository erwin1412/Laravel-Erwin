<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes , HasUuids;

    protected $guarded = [];


    protected $fillable = [
        "id",
        "image",
        'products_id',
    ];

    public function getUrlImage($url){
        return config('app.url').Storage::url($url);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id', 'id');
    }



}
