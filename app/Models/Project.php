<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project';

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'name',
        'customer_id',
        'supplier_id',
        'vendor',
        'accountManagerName',
        'productCategory',
        'type',
        'status',
        'is_active',
        'date',
        'end_date',
        'remark',
        'cost',
        'revenue',
        'log_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_date' => 'date',
            'cost' => 'decimal:2',
            'revenue' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'customer_id'
        );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id',
            'supplier_id'
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'project_id',
            'project_id'
        );
    }
    public function costItems(): HasMany
    {
        return $this->hasMany(ProjectCost::class, 'project_id', 'project_id')
            ->orderByDesc('item_date')
            ->orderByDesc('project_cost_id');
    }

    public function revenueItems(): HasMany
    {
        return $this->hasMany(ProjectRevenue::class, 'project_id', 'project_id')
            ->orderByDesc('item_date')
            ->orderByDesc('project_revenue_id');
    }
}
