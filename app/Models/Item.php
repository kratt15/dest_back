<?php

namespace App\Models;

use App\Models\Brands;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'name',
        'reference',
        'expiration_date',
        'cost',
        'price',
        'description'
    ];

    //// LINKS

    // Un article a une seule catégorie
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }
    // un article a une seule marque
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brands::class)->withTrashed();
    }

    // Un article a un seul fournisseur
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class)->withTrashed();
    }

    // Relation Stocker(article-magasin)
    public function stores_stock(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'item_store')->withTimestamps()->withPivot('quantity', 'security_quantity', 'deleted_at')->withTrashed();
    }
    // Fin Relation Stocker(article-magasin)

    // Relation Appartenir(article-commande)
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)->withTimestamps()->withPivot('quantity', 'deleted_at')->withTrashed();
    }
    // Fin Relation Appartenir(article-commande)

    // Relation Transférer(calendrier-article-magasin)
    public function calendars_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }

    public function stores_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }
    // Fin Relation Transférer(calendrier-article-magasin)

    // Relation Contenir(article-achat)
    public function purchases(): BelongsToMany
    {
        return $this->belongsToMany(Purchase::class)->withTimestamps()->withPivot('quantity', 'deleted_at')->withTrashed();
    }
    // Fin Relation Contenir(article-achat)

    //// END OF LINKS

    public function toSearchableArray()
    {
        $searchArray = [

            'name' => $this->name,
            'reference' => $this->reference,
            'expiration_date' => $this->expiration_date,
            'cost' => $this->cost,
            'price' => $this->price,
            'description' => $this->description,
            'created_at' => $this->created_at,


        ];

        return $searchArray;
    }
}
