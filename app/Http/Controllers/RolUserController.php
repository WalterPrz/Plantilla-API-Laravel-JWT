<?php

namespace App\Http\Controllers;

use App\Models\RolUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class RolUserController extends Controller
{

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make([
                'user_id' => $request->user_id,
                'roles' => $request->roles,
            ], [
                'user_id' => 'required|exists:users,id',
                'roles' => 'array',
                'roles.*' => 'exists:rols,id',
            ]);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data_guardada = RolUser::where('user_id', $request->user_id)->get();
            /* Proceso para sacar las diferencias de ids */
            $roles_ids_guardados = $data_guardada->map(function (RolUser $item, int $key) {
                return $item->rol_id;
            });
            //array que sera para eliminar
            $dataDiffToDelete = collect([]);
            if (count($roles_ids_guardados) > 0) {
                $dataDiffToDelete = $roles_ids_guardados->diff($request->roles);
            }
            $dataDiffToDelete->toArray();
            //array que será para insertar
            $dataDiffToInsert = collect($request->roles)->diff($roles_ids_guardados);
            $dataDiffToInsertUniques = $dataDiffToInsert->unique();
            $user = User::find($request->user_id);
            RolUser::where('user_id', $request->user_id)->whereIn('rol_id', $dataDiffToDelete)->delete();
            $user->rols()->attach($dataDiffToInsertUniques);
            DB::commit();
            return  response()->json(["message"=>'Actualizado exitosamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
