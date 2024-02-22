<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'name_provider',
        'name_resp',
        'address_provider',
        'phone_provider',
        'email_provider',
    ];

    // Un fournisseur peut fournir plusieurs articles
    public function items()
    {
        return $this->hasMany(Item::class)->withTrashed();
    }
    public function toSearchableArray()
    {
        $searchArray = [

            'name_provider' => $this->name_provider,
            'name_resp' => $this->name_resp,
            'address_provider' => $this->address_provider,
            'phone_provider' => $this->phone_provider,
            'email_provider' => $this->email_provider,

        ];

        return $searchArray;
    }
}
