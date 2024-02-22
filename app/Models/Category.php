<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    use Searchable;

    protected $fillable = [
        'title',
    ];

    // Une catégorie peut être attribuée à plusieurs articles
    public function items()
    {
        return $this->hasMany(Item::class)->withTrashed();
    }

    public function toSearchableArray()
    {
        $searchArray = [

            'title' => $this->title,
        ];

        return $searchArray;
    }
}
