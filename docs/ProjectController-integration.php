<?php

// Merge these patterns into your existing ProjectController.

use App\Http\Controllers\Concerns\UsesRecordAccess;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Inside the class:
use UsesRecordAccess;

// INDEX: start the existing query like this.
$query = $this->accessibleQuery(
    Project::query(),
    $request->user(),
    'project_access_scope'
);

// CREATE / EDIT: pass active users to the form.
$assignableUsers = User::query()
    ->where('status', 'Active')
    ->orderBy('name')
    ->get(['user_id', 'name']);

// STORE / UPDATE validation.
'assigned_user_id' => [
    'nullable',
    'integer',
    Rule::exists('users', 'user_id')->where('status', 'Active'),
],

// STORE values.
'created_by' => $request->user()->user_id,
'assigned_user_id' => $validated['assigned_user_id'] ?? $request->user()->user_id,

// UPDATE: never change created_by. Only update assignment when supplied.
'assigned_user_id' => $validated['assigned_user_id'] ?? $project->assigned_user_id,

// SHOW authorization.
$this->authorizeRecord(
    $request->user(),
    $project,
    'project_access_scope',
    'projects.view'
);

// EDIT / UPDATE authorization.
$this->authorizeRecord(
    $request->user(),
    $project,
    'project_access_scope',
    'projects.edit'
);
