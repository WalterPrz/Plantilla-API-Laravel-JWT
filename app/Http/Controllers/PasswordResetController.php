<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */

    public function create(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'Si existe un registro con ese email, se enviará un correo electrónico.'
                ]);
            }
            $token = Str::random(64);
            $frontendUrl = config('app.frontend_url');
            $frontend_view_path_password_reset = config('app.frontend_view_path_password_reset');
            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => $token,
                ]
            );
            if ($user && $passwordReset) {
                Mail::to($user->email, $user->name)->send(new ForgotPassword($token, $user->name, "{$frontendUrl}{$frontend_view_path_password_reset}{$token}"));
            }
            //Log::info(["token" => $token, "name" => $user->name, "url" => "Hello {$frontendUrl}/{$token}"]);
            return response()->json([
                'message' => 'Si existe un registro con ese email, se enviará un correo electrónico'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'Este no es correcto.'
            ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'El password reset es incorrecto.'
            ], 404);
        }
        return response()->json($passwordReset);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'El token es inválido'
            ], 404);
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return response()->json([
                'message' => 'No se encontró información de ese correo.'
            ], 404);
        $user->password =  Hash::make($request->password);
        $user->save();
        $passwordReset->delete();
        return response()->json([
            'message' => 'contraseña cambiada con éxito'
        ]);
    }
}
