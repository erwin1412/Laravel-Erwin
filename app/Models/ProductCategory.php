<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes , HasUuids;

    protected $fillable = [
        "id",
        "name",
    ];
    public function products(){
        return $this->hasMany(Product::class,"categories_id","id");
    }

}
