<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $name = $request->input('name');
            $activo = $request->input('activo');
            $paginate = $request->query('pagination', false);
            $perPage = $request->query('per_page', 10);
            $currentPage = $request->query('page', 1);
            $query = User::with('rols');
            $data =  $paginate ? $query->paginate($perPage, ['*'], 'page', $currentPage) : $query->get();
            return response()->json($data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function show(Request $request, $user)
    {
        try {

            $data = User::with('rols')->where('id', $user)->firstOrFail();
            return response()->json($data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'active' => true
            ]);
            return response()->json(['message' => 'Creado Exitosamente'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function update(Request $request, $user)
    {
        try {
            $data = User::where('id', $user)->first();
            if (!$data) {
                return response()->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users',
                'activo' => 'boolean'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($data) {
                $data->fill($request->only(['name', 'email', 'activo']));
                $data->save();
                return response()->json(['message' => 'Usuario actualizado exitosamente'], JsonResponse::HTTP_OK);
            }
            return response()->json(['data' => $data, 'message' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_MODIFIED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function destroy($user)
    {
        try {
            $data = User::where('id', $user)->first();
            if (!$data) {
                return response()->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
            }
            $data->active = !$data->active;
            $data->save();
            $message = $data->active == true ? 'Usuario dado de alta correctamente ' : 'Usuario dado de baja correctamente';
            return response()->json(['message' => $message], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
