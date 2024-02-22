<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'name',
        'location',
    ];

    //// LINKS

    // Un magasin peut effectuer plusieurs commandes
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->withTrashed();
    }

    // Un magasin peut recevoir plusieurs achats
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class)->withTrashed();
    }

    // Relation Stocker(article-magasin)
    public function items_stock(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_store')->withTimestamps()->withPivot('quantity', 'security_quantity', 'deleted_at')->withTrashed();
    }
    // Fin Relation Stocker(article-magasin)

    // Relation Gérer(calendrier-gérant-magasin)
    public function calendars_manage(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }

    public function users_manage(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }
    // Fin Relation Gérer(calendrier-gérant-magasin)


    // Relation Transférer(calendrier-article-magasin)
    public function calendars_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }

    public function items_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }
    // Fin Relation Transférer(calendrier-article-magasin)

    //// END OF LINKS

    public function toSearchableArray()
    {
        $searchArray = [

            'name' => $this->name,
            'location' => $this->location,
        ];

        return $searchArray;
    }
}
