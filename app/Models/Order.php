<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'status',
        'total',
        'pickup_address',
        'pickup_date',
        'internal_notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'pickup_date' => 'date',
    ];

    /**
     * Relationship: An Order is placed by one customer.
     * Question 2(a)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship: Order belongs to a branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relationship: An Order can include many services, and a service appears in many orders.
     * Question 2(a)
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('quantity', 'subtotal')
            ->withTimestamps();
    }

    /**
     * Relationship: An Order has exactly one payment record.
     * Question 2(a)
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
