<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'notes',
        'status',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            'customer_id',
            'customer_id'
        );
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(
            CustomerContact::class,
            'customer_id',
            'customer_id'
        )
            ->orderByDesc('is_primary')
            ->orderBy('customer_contact_id');
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(
            CustomerContact::class,
            'customer_id',
            'customer_id'
        )->where('is_primary', true);
    }
}
