<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPermiso extends Model
{
    use HasFactory;
    protected $fillable = ['nombre'];
    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class);
    }
}
