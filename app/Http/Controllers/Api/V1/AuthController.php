<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Iniciar sesión y generar token de Sanctum
     */
    public function login(Request $request)
    {
        // Validar los campos de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar con las credenciales
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'error' => [
                    'status' => 422,
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'Las credenciales proporcionadas son incorrectas.'
                ]
            ], 422);
        }

        $user = Auth::user();

        // Generar el token de acceso personal con Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /**
     * Cerrar sesión (Revocar token actual)
     */
    public function logout(Request $request)
    {
        // Elimina el token que usó el usuario para hacer la petición
        $request->user()->currentAccessToken()->delete();

        // Retorna respuesta vacía con código 204 (No Content)
        return response()->noContent();
    }
}