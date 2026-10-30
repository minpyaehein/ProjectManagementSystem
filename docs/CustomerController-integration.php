<?php

// Merge these patterns into your existing CustomerController.

use App\Http\Controllers\Concerns\UsesRecordAccess;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Validation\Rule;

use UsesRecordAccess;

$query = $this->accessibleQuery(
    Customer::query(),
    $request->user(),
    'customer_access_scope'
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
'assigned_user_id' => $validated['assigned_user_id'] ?? $customer->assigned_user_id,

// SHOW
$this->authorizeRecord(
    $request->user(),
    $customer,
    'customer_access_scope',
    'customers.view'
);

// EDIT / UPDATE
$this->authorizeRecord(
    $request->user(),
    $customer,
    'customer_access_scope',
    'customers.edit'
);

// ACTIVATE / DEACTIVATE
$this->authorizeRecord(
    $request->user(),
    $customer,
    'customer_access_scope',
    'customers.deactivate'
);
