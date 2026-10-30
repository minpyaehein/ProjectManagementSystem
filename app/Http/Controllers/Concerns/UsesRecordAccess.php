<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Support\RecordAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait UsesRecordAccess
{
    protected function accessibleQuery(Builder $query, User $user, string $scopeField): Builder
    {
        return RecordAccess::apply($query, $user, $scopeField);
    }

    protected function authorizeRecord(User $user, Model $record, string $scopeField, string $permission): void
    {
        RecordAccess::authorize($user, $record, $scopeField, $permission);
    }
}
