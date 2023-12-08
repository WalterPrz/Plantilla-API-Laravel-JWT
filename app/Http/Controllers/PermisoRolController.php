<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permiso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\Rol;
use App\Models\PermisoRol;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PermisoRolController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make([
                'rol_id' => $request->rol_id,
                'permisos' => $request->permisos,
            ], [
                'rol_id' => 'required|exists:rols,id',
                'permisos' => 'array',
                'permisos.*' => 'exists:permisos,id',
            ]);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data_guardada = PermisoRol::where('rol_id', $request->rol_id)->get();
            /* Proceso para sacar las diferencias de ids */
            $permiso_ids_guardados = $data_guardada->map(function (PermisoRol $item, int $key) {
                return $item->permiso_id;
            });
            //array que sera para eliminar
            $dataDiffToDelete = collect([]);
            if (count($permiso_ids_guardados) > 0) {
                $dataDiffToDelete = $permiso_ids_guardados->diff($request->permisos);
            }
            $dataDiffToDelete->toArray();
            //array que será para insertar
            $dataDiffToInsert = collect($request->permisos)->diff($permiso_ids_guardados);
            $dataDiffToInsertUniques = $dataDiffToInsert->unique();
            $rol = Rol::find($request->rol_id);
            PermisoRol::where('rol_id', $request->rol_id)->whereIn('permiso_id', $dataDiffToDelete)->delete();
            $rol->permisos()->attach($dataDiffToInsertUniques);
            DB::commit();
            return  response()->json(["message"=>'Actualizado exitosamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
