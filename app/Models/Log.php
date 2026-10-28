<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Log extends Model
{
    use HasFactory;

    protected $table = 'log';

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'status',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            'log_id',
            'log_id'
        );
    }
}
