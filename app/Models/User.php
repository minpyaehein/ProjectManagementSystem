<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Concerns\HasRecordAssignments;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRecordAssignments;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'project_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'role_id'
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
            'project_id',
            'project_id'
        );
    }
public function hasPermission(string $permissionSlug): bool
{
    $this->loadMissing('role.permissions');

    if (! $this->role) {
        return false;
    }

    if (
        $this->role->permissions->contains(
            'slug',
            'admin.full'
        )
    ) {
        return true;
    }

    return $this->role->permissions->contains(
        'slug',
        $permissionSlug
    );
}
    public function hasAnyPermission(array $slugs): bool
    {
        $this->loadPermissionRelations();

        if (!$this->role) {
            return false;
        }

        if (
            $this->role->permissions->contains(
                'slug',
                'admin.full'
            )
        ) {
            return true;
        }

        return $this->role
            ->permissions
            ->whereIn('slug', $slugs)
            ->isNotEmpty();
    }

    private function loadPermissionRelations(): void
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role.permissions');

            return;
        }

        if (
            $this->role &&
            !$this->role->relationLoaded('permissions')
        ) {
            $this->role->load('permissions');
        }
    }
}
