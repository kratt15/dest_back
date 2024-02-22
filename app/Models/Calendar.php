<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'date_time',
    ];

    //// LINKS

    // Relation Gérer(calendrier-gérant-magasin)

    public function users_manage(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }

    public function stores_manage(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }

    // Fin Relation Gérer(calendrier-gérant-magasin)


    // Relation Transférer(calendrier-article-magasin)

    public function managers_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Manager::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }

    public function stores_transfer(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'calendar_item_store')->withTimestamps()->withPivot('quantity', 'destination_store', 'deleted_at')->withTrashed();
    }

    // Fin Relation Transférer(calendrier-article-magasin)

    //// END OF LINKS
}
