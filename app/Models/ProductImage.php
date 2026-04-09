<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'url',
        'mime',
        'size',
        'position',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute($value)
    {
        if ($this->path && Storage::disk('public')->exists($this->path)) {
            return URL::to(Storage::url($this->path));
        }

        return URL::to('/img/noimage.png');
    }
}
