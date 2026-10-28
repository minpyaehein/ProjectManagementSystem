<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'name',
        'contact_person',
        'category',
        'email',
        'phone',
        'address',
        'status',
        'notes',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(
            SupplierContact::class,
            'supplier_id',
            'supplier_id'
        )
            ->orderByDesc('is_primary')
            ->orderBy('supplier_contact_id');
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(
            SupplierContact::class,
            'supplier_id',
            'supplier_id'
        )->where('is_primary', true);
    }
}
