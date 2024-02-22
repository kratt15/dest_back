<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Order extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    use Notifiable;

    protected $fillable = [
        'issue_date',
        'reception_date',
        'predicted_date',
    ];

    //// LINKS

    // Une commande est faite par un seul magasin
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class)->withTrashed();
    }

    // Relation Appartenir(article-commande)

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withTimestamps()->withPivot('quantity', 'deleted_at')->withTrashed();
    }

    // Fin Relation Appartenir(article-commande)

    //// END OF LINKS

    // public function toSearchableArray() {
    //     $searchArray =[


    //     ];

    //     return $searchArray;
    // }
}
