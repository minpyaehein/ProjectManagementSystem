<?php

// Merge into Project.php, Customer.php, and Supplier.php.

use App\Models\Concerns\HasRecordOwnership;

// Inside each model class:
use HasRecordOwnership;

// Add to the existing $fillable array:
'created_by',
'assigned_user_id',
