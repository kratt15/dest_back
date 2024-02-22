<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'name_customer',
        'phone_customer',
        'address_customer',
    ];

    //// LINKS

    // Un client peut faire plusieurs achats
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class)->withTrashed();
    }

    // Un client peut engager plusieurs payements
    // public function payments(): HasMany
    // {
    //     return $this->hasMany(Payment::class);
    // }
    public function toSearchableArray()
    {
        $searchArray = [

            'name_customer' => $this->name_customer,
            'phone_customer' => $this->phone_customer,
            'address_customer' => $this->address_customer,

        ];

        return $searchArray;
    }

    //// END OF LINKS
}
