<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método para hacer login y obtener el token
    public function login(Request $request)
    {
        // ✅ Validar entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);

        // ❌ Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
            /* return response()->json(['error' => 'Unauthorized'], 401); */
        }

        return $this->respondWithToken($token);
    }

    // Obtener información del usuario autenticado
    public function me()
    {
        //return response()->json(Auth::guard('api')->user()->load('person'));
        $user = Auth::user();
        //$userPermissions = $user->getAllPermissions()->pluck('id');

        // Obtener módulos principales con sus hijos, filtrando por permisos
        // Si el usuario tiene acceso a todos los módulos, los obtenemos todos
        /* $modules = Module::whereNull('parent_id') // Solo módulos principales
        ->with('children') // Cargamos los hijos
        ->get(); */

        return response()->json([
            'user' => $user->load('roles', 'permissions','offices', 'person'),
            //'menu' => $modules,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    // Cerrar sesión (invalidar token)
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Refrescar token
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    // Respuesta del token
    protected function respondWithToken($token)
    {


        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
