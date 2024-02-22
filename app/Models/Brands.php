<?php

namespace App\Models;

use App\Models\Item;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brands extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $fillable = [
        'title',
    ];

    // Une marque peut être attribuée à plusieurs articles
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
