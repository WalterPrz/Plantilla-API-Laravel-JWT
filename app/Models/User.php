<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active'
    ];
    public function rols(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class)->withTimestamps();
    }
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
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
    public function scopeActivo(Builder $query, $type): void
    {
        if (isset($type)) {
            $query->where('activo', $type);
        }
    }
    public function scopeName(Builder $query,  $name): void
    {
        if (isset($name)) {
            $normalizedText = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
            $lowercasedText = mb_strtolower($normalizedText, 'UTF-8');
            $trimmedText = trim($lowercasedText);
            //Si es con Mysql : 
            $query->whereRaw("LOWER(name) COLLATE utf8mb4_unicode_ci LIKE ?", ["%{$trimmedText}%"]);
        }
    }
}
