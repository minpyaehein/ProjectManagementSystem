<?php

namespace App\Models\Concerns;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRecordAssignments
{
    public function assignedProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_user_assignments',
            'user_id',
            'project_id',
            'user_id',
            'project_id'
        )->withTimestamps();
    }

    public function assignedCustomers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_user_assignments',
            'user_id',
            'customer_id',
            'user_id',
            'customer_id'
        )->withTimestamps();
    }

    public function assignedSuppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            Supplier::class,
            'supplier_user_assignments',
            'user_id',
            'supplier_id',
            'user_id',
            'supplier_id'
        )->withTimestamps();
    }
}
