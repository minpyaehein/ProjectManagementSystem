<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class payment extends Model
{
    //
    use HasFactory;

    protected $fillable = ['type','account_number','account_name'];
}
