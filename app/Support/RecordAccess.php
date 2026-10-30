<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecordAccess
{
    public const OWN = 'own';

    public const ASSIGNED = 'assigned';

    public const ALL = 'all';

    public static function apply(
        Builder $query,
        User $user,
        string $roleScopeField,
        string $ownerColumn = 'created_by',
        string $assignedColumn = 'assigned_user_id'
    ): Builder {
        $scope = (string) (
            $user->role?->{$roleScopeField}
            ?? self::ASSIGNED
        );

        /*
         * All scope has no record-level restriction.
         * Action permission checks still apply separately.
         */
        if ($scope === self::ALL) {
            return $query;
        }

        $model = $query->getModel();

        $qualifiedOwnerColumn = $model->qualifyColumn(
            $ownerColumn
        );

        $qualifiedAssignedColumn = $model->qualifyColumn(
            $assignedColumn
        );

        $qualifiedPrimaryKey = $model->qualifyColumn(
            $model->getKeyName()
        );

        $userId = (int) $user->user_id;

        /*
         * Own Only:
         * Show records created by the logged-in user.
         */
        if ($scope === self::OWN) {
            return $query->where(
                $qualifiedOwnerColumn,
                $userId
            );
        }

        /*
         * Assigned:
         * Support three access paths:
         *
         * 1. The user created the record.
         * 2. The record's assigned_user_id is the user.
         * 3. The user is selected in the module assignment
         *    pivot table.
         */
        $assignment = self::assignmentConfiguration(
            $roleScopeField
        );

        return $query->where(
            function (Builder $accessQuery) use (
                $qualifiedOwnerColumn,
                $qualifiedAssignedColumn,
                $qualifiedPrimaryKey,
                $assignment,
                $userId
            ): void {
                $accessQuery
                    ->where(
                        $qualifiedOwnerColumn,
                        $userId
                    )
                    ->orWhere(
                        $qualifiedAssignedColumn,
                        $userId
                    );

                if ($assignment !== null) {
                    $accessQuery->orWhereExists(
                        function ($assignmentQuery) use (
                            $assignment,
                            $qualifiedPrimaryKey,
                            $userId
                        ): void {
                            $assignmentQuery
                                ->selectRaw('1')
                                ->from(
                                    $assignment['table']
                                )
                                ->whereColumn(
                                    $assignment['table']
                                        . '.'
                                        . $assignment[
                                            'record_key'
                                        ],
                                    $qualifiedPrimaryKey
                                )
                                ->where(
                                    $assignment['table']
                                        . '.user_id',
                                    $userId
                                );
                        }
                    );
                }
            }
        );
    }

    public static function authorize(
        User $user,
        Model $record,
        string $roleScopeField,
        string $requiredPermission,
        string $ownerColumn = 'created_by',
        string $assignedColumn = 'assigned_user_id'
    ): void {
        /*
         * Permission determines the allowed action.
         */
        abort_unless(
            $user->hasPermission($requiredPermission),
            403
        );

        $scope = (string) (
            $user->role?->{$roleScopeField}
            ?? self::ASSIGNED
        );

        /*
         * All scope can access every record.
         */
        if ($scope === self::ALL) {
            return;
        }

        $userId = (int) $user->user_id;

        $ownerId = (int) (
            $record->getAttribute($ownerColumn)
            ?? 0
        );

        $assignedUserId = (int) (
            $record->getAttribute($assignedColumn)
            ?? 0
        );

        /*
         * Own Only requires ownership.
         */
        if ($scope === self::OWN) {
            abort_unless(
                $ownerId === $userId,
                403
            );

            return;
        }

        /*
         * Preserve compatibility with records using the
         * existing created_by or assigned_user_id columns.
         */
        if (
            $ownerId === $userId
            || $assignedUserId === $userId
        ) {
            return;
        }

        /*
         * Check the new many-to-many assignment table.
         */
        $assignment = self::assignmentConfiguration(
            $roleScopeField
        );

        abort_unless(
            $assignment !== null
            && self::hasPivotAssignment(
                $user,
                $record,
                $assignment
            ),
            403
        );
    }

    /**
     * Return the assignment configuration for a module.
     *
     * @return array{
     *     table: string,
     *     record_key: string
     * }|null
     */
    private static function assignmentConfiguration(
        string $roleScopeField
    ): ?array {
        return match ($roleScopeField) {
            'project_access_scope' => [
                'table' => 'project_user_assignments',
                'record_key' => 'project_id',
            ],

            'customer_access_scope' => [
                'table' => 'customer_user_assignments',
                'record_key' => 'customer_id',
            ],

            'supplier_access_scope' => [
                'table' => 'supplier_user_assignments',
                'record_key' => 'supplier_id',
            ],

            default => null,
        };
    }

    /**
     * Check whether the user is assigned to a specific record.
     *
     * @param array{
     *     table: string,
     *     record_key: string
     * } $assignment
     */
    private static function hasPivotAssignment(
        User $user,
        Model $record,
        array $assignment
    ): bool {
        return $record
            ->getConnection()
            ->table($assignment['table'])
            ->where(
                'user_id',
                $user->user_id
            )
            ->where(
                $assignment['record_key'],
                $record->getKey()
            )
            ->exists();
    }
}
