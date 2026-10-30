<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRevenue extends Model
{
    protected $table = 'project_revenues';
    protected $primaryKey = 'project_revenue_id';

    protected $fillable = [
        'project_id',
        'item_date',
        'description',
        'amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'item_date' => 'date',
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
