<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
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
        'created_by',
        'assigned_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'cost' => 'decimal:2',
            'revenue' => 'decimal:2',
            'created_by' => 'integer',
            'assigned_user_id' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'project_id', 'project_id');
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
