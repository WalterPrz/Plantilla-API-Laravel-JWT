<?php

namespace App\Http\Middleware;

use App\Models\Permiso;
use App\Models\Rol;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class VerifyPermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        if (!config('app.allow_validation_by_permission')) {
            return $next($request);
        }
        $user = auth()->user();
        $permisos = Permiso::select(['nombre'])->with(['rols'])->whereHas('rols.users', function (Builder $query) use ($user) {
            $query->where('users.id', $user->id);
        })->get();
        $permisosCollection = collect($permisos);
        $existe = $permisosCollection->firstWhere('nombre', $permiso);
        if ($existe === null) {
            throw new AuthorizationException('No tienes los permisos necesarios para realizar esta acción.');
        }
        return $next($request);
    }
}
