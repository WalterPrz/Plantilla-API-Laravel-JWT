<?php

namespace App\Http\Controllers;

use App\Models\TipoPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ForeignKeyConstraintException;

class TipoPermisoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $paginate = $request->query('pagination', false);
            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $query = TipoPermiso::query();
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
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $tipoPermiso = TipoPermiso::create($request->all());
            return response()->json(['data' => $tipoPermiso, 'message' => 'Creado Exitosamente'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function update(Request $request, $tipoPermiso)
    {
        try {
            $data = TipoPermiso::where('id', $tipoPermiso)->first();
            if (!$data) {
                return response()->json(['error' => 'Tipo permiso no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data->nombre = $request->nombre;
            $data->save();
            return response()->json(['data' => $data, 'message' => 'Actualizado Exitosamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function destroy($tipoPermiso)
    {
        try {
            $data = TipoPermiso::where('id', $tipoPermiso)->with('permisos')->first();
            if (!$data) {
                return response()->json(['error' => 'Tipo de permiso no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if (count($data->permisos) > 0) {
                throw new ForeignKeyConstraintException();
            }
            $data->delete();
            return response()->json(['message' => 'Tipo de permiso eliminado correctamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
