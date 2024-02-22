<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'ref_payment',
        'amount',
        'payment_date_time',
    ];

    //// LINKS

    // Un payement comble un et un seul achat
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    // Un payement est engagé par un seul client
    // public function customer(): BelongsTo
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    //// END OF LINKS
}
