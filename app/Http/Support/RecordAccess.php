<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecordAccess
{
    public static function apply(Builder $query, User $user, string $scopeField): Builder
    {
        $scope = (string) ($user->role?->{$scopeField} ?? 'assigned');
        if ($scope === 'all') return $query;

        $owner = $query->getModel()->qualifyColumn('created_by');
        $assigned = $query->getModel()->qualifyColumn('assigned_user_id');
        $userId = (int) $user->user_id;

        if ($scope === 'own') return $query->where($owner, $userId);

        return $query->where(function (Builder $subQuery) use ($owner, $assigned, $userId): void {
            $subQuery->where($assigned, $userId)->orWhere($owner, $userId);
        });
    }

    public static function authorize(User $user, Model $record, string $scopeField, string $permission): void
    {
        abort_unless($user->hasPermission($permission), 403);
        $scope = (string) ($user->role?->{$scopeField} ?? 'assigned');
        if ($scope === 'all') return;

        $userId = (int) $user->user_id;
        $ownerId = (int) ($record->getAttribute('created_by') ?? 0);
        $assignedId = (int) ($record->getAttribute('assigned_user_id') ?? 0);

        if ($scope === 'own') {
            abort_unless($ownerId === $userId, 403);
            return;
        }

        abort_unless($ownerId === $userId || $assignedId === $userId, 403);
    }
}
