<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Scout\Searchable;
// use App\Http\Middleware\TrustHosts;
// use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{

    use HasApiTokens, HasFactory, Notifiable, HasRoles, Searchable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'statut',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    //// LINKS

    // Relation Gérer(calendrier-gérant-magasin)

    public function calendars_manage(): BelongsToMany
    {
        return $this->belongsToMany(Calendar::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }

    public function stores_manage(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'calendar_store_user')->withTimestamps()->withPivot('deleted_at')->withTrashed();
    }

    // Fin Relation Gérer(calendrier-gérant-magasin)

    //// END OF LINKS

    public function toSearchableArray()
    {
        $searchArray = [

            'name' => $this->name,
            'email' => $this->email

        ];

        return $searchArray;
    }

    public function sendPasswordResetNotification($token): void
{
    $url = 'https://oba-felix-fale.vercel.app/reset-password?token='.$token;

    $this->notify(new ResetPasswordNotification($url));
}
}
