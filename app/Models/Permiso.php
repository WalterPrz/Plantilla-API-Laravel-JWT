<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permiso extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'tipo_permiso_id'];
    public function tipo_permiso(): BelongsTo
    {
        return $this->belongsTo(TipoPermiso::class);
    }
    public function rols(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class)->withTimestamps();
    }
    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => strtoupper(Str::slug($value, '_')),
        );
    }
}
