<?php

namespace App\Http\Controllers;

use App\Exceptions\ForeignKeyConstraintException;
use App\Models\Permiso;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $paginate = $request->query('pagination', false);
            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);

            $query = Permiso::with('tipo_permiso');
            $data =  $paginate ? $query->paginate($perPage, ['*'], 'page', $currentPage) : $query->get();
            return response()->json($data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'tipo_permiso_id' => 'required|exists:tipo_permisos,id'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $permiso = new Permiso();
            $permiso->nombre =  $request->nombre;
            $permiso->tipo_permiso_id = $request->tipo_permiso_id;
            $permiso->save();
            return response()->json(['data' => $permiso, 'message' => 'Creado Exitosamente'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, $permiso)
    {
        try {
            $data = Permiso::where('id', $permiso)->first();
            if (!$data) {
                return response()->json(['error' => 'Permiso no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $validator = Validator::make($request->all(), [
                'nombre' => 'string|max:255',
                'tipo_permiso_id' => 'exists:tipo_permisos,id'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data->fill($request->only(['nombre', 'tipo_permiso_id']));
            $data->save();
            return response()->json(['data' => $data, 'message' => 'Actualizado Exitosamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function destroy($permiso)
    {
        try {
            $data = Permiso::where('id', $permiso)->with('rols')->first();
            if (!$data) {
                return response()->json(['error' => 'Permiso no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if (count($data->rols) > 0) {
                throw new ForeignKeyConstraintException();
            }
            $data->delete();
            return response()->json(['message' => 'Permiso eliminado correctamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
