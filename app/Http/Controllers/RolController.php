<?php

namespace App\Http\Controllers;

use App\Exceptions\ForeignKeyConstraintException;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    public function index(Request $request)
    {
        try {
            $paginate = $request->query('pagination', false);
            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);

            $query = Rol::with('permisos');
            $data =  $paginate ? $query->paginate($perPage, ['*'], 'page', $currentPage) : $query->get();
            return response()->json($data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function show(Request $request, $rol)
    {
        try {
            $data = Rol::with('permisos')->where('id', $rol)->firstOrFail();
            return response()->json(['data' => $data], JsonResponse::HTTP_OK);
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
            $rol = Rol::create($request->all());

            return response()->json(['data' => $rol, 'message' => 'Creado Exitosamente'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function update(Request $request,$rol)
    {
        try {
            $data = Rol::where('id', $rol)->first();
            if (!$data) {
                return response()->json(['error' => 'Rol no encontrado'], JsonResponse::HTTP_NOT_FOUND);
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
    public function destroy($rol)
    {
        try {
            $data = Rol::where('id', $rol)->with('users')->with('permisos')->first();
            if (!$data) {
                return response()->json(['error' => 'Rol no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            if (count($data->users) > 0 || count($data->permisos) > 0) {
                throw new ForeignKeyConstraintException("Tiene datos relacionados.");
            }
            $data->delete();
            return response()->json(['message' => 'Rol eliminado correctamente'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
