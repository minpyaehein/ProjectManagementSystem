<?php

// Merge these patterns into your existing SupplierController.

use App\Http\Controllers\Concerns\UsesRecordAccess;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Validation\Rule;

use UsesRecordAccess;

$query = $this->accessibleQuery(
    Supplier::query(),
    $request->user(),
    'supplier_access_scope'
);

$assignableUsers = User::query()
    ->where('status', 'Active')
    ->orderBy('name')
    ->get(['user_id', 'name']);

'assigned_user_id' => [
    'nullable',
    'integer',
    Rule::exists('users', 'user_id')->where('status', 'Active'),
],

// STORE
'created_by' => $request->user()->user_id,
'assigned_user_id' => $validated['assigned_user_id'] ?? $request->user()->user_id,

// UPDATE
'assigned_user_id' => $validated['assigned_user_id'] ?? $supplier->assigned_user_id,

// SHOW
$this->authorizeRecord(
    $request->user(),
    $supplier,
    'supplier_access_scope',
    'suppliers.view'
);

// EDIT / UPDATE
$this->authorizeRecord(
    $request->user(),
    $supplier,
    'supplier_access_scope',
    'suppliers.edit'
);

// ACTIVATE / DEACTIVATE
$this->authorizeRecord(
    $request->user(),
    $supplier,
    'supplier_access_scope',
    'suppliers.deactivate'
);
