<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class products extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','description','category_id','count','image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
