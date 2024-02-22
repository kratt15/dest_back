<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Purchase extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'ref_purchase',
        'purchase_date_time',
    ];

    //// LINKS

    // Un achat peut entraîner plusieurs payements
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->withTrashed();
    }

    // Un achat n'est passé que par un client
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    // Un achat est effectué dans un seul magasin
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class)->withTrashed();
    }

    // Relation Contenir(article-achat)

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withTimestamps()->withPivot('quantity', 'deleted_at')->withTrashed();
    }

    // Fin Relation Contenir(article-achat)

    //// END OF LINKS

}
