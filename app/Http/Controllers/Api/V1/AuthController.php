<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController
 * 
 * Controlador para autenticación API
 * Maneja:
 * - Login
 * - Registro
 * - Logout
 * - Token refresh
 * - Datos del usuario actual
 * - Cambio de contraseña
 */
class AuthController extends BaseController
{
    /**
     * POST /api/v1/auth/login
     * 
     * Autentica un usuario y retorna token
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();

            // Buscar usuario por email
            $user = User::where('email', $credentials['email'])
                ->where('activo', 1)
                ->first();

            // Validar credenciales
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return $this->respondUnauthorized('Email o contraseña incorrectos');
            }

            // Crear token Sanctum
            $token = $user->createToken('api-token', ['*'])->plainTextToken;

            // Registrar auditoría de login
            $this->registrarAuditoria(
                $user->id,
                'AUTH',
                'LOGIN',
                $user->id,
                null,
                ['accion' => 'Login exitoso']
            );

            return $this->respondSuccess(
                [
                    'token' => $token,
                    'type' => 'Bearer',
                    'user' => new UserResource($user),
                    'expires_in' => config('sanctum.expiration', 1440) * 60, // en segundos
                ],
                'Autenticación exitosa',
                200
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error en autenticación', $e);
        }
    }

    /**
     * POST /api/v1/auth/register
     * 
     * Registra un nuevo usuario
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Verificar si usuario ya existe
            if (User::where('email', $data['email'])->exists()) {
                return $this->respondError(
                    'El email ya está registrado',
                    409
                );
            }

            // Crear usuario
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'idPersona' => $data['idPersona'],
                'idRol' => 3, // Usuario regular por defecto
                'activo' => 1,
            ]);

            // Crear token
            $token = $user->createToken('api-token', ['*'])->plainTextToken;

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'USER',
                'CREATE',
                $user->id,
                null,
                ['accion' => 'Registro de nuevo usuario']
            );

            return $this->respondCreated(
                [
                    'token' => $token,
                    'type' => 'Bearer',
                    'user' => new UserResource($user),
                    'expires_in' => config('sanctum.expiration', 1440) * 60,
                ],
                'Usuario registrado exitosamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error en registro', $e);
        }
    }

    /**
     * POST /api/v1/auth/logout
     * 
     * Cierra la sesión del usuario actual
     * Invalida el token actual
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user) {
                // Revocar token actual
                $user->currentAccessToken()->delete();

                // Registrar auditoría
                $this->registrarAuditoria(
                    $user->id,
                    'AUTH',
                    'LOGOUT',
                    $user->id,
                    null,
                    ['accion' => 'Logout exitoso']
                );
            }

            return $this->respondSuccess(
                null,
                'Sesión cerrada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al cerrar sesión', $e);
        }
    }

    /**
     * POST /api/v1/auth/refresh
     * 
     * Regenera un nuevo token para el usuario
     * (Útil cuando el token está próximo a expirar)
     * 
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->respondUnauthorized('Usuario no autenticado');
            }

            // Crear nuevo token
            $newToken = $user->createToken('api-token', ['*'])->plainTextToken;

            // Revocar token anterior
            $user->currentAccessToken()->delete();

            return $this->respondSuccess(
                [
                    'token' => $newToken,
                    'type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration', 1440) * 60,
                ],
                'Token renovado correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al renovar token', $e);
        }
    }

    /**
     * GET /api/v1/auth/me
     * 
     * Retorna los datos del usuario autenticado
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->respondUnauthorized('Usuario no autenticado');
            }

            // Cargar relaciones
            $user->load('persona', 'rol');

            return $this->respondSuccess(
                new UserResource($user),
                'Datos del usuario obtenidos'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al obtener datos', $e);
        }
    }

    /**
     * POST /api/v1/auth/change-password
     * 
     * Cambia la contraseña del usuario actual
     * 
     * @return JsonResponse
     */
    public function changePassword(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->respondUnauthorized('Usuario no autenticado');
            }

            // Validar datos
            $data = request()->validate([
                'current_password' => 'required|string',
                'new_password' => ['required', 'string', 'min:8', 'confirmed', Password::min(8)->mixedCase()->symbols()],
            ]);

            // Validar contraseña actual
            if (!Hash::check($data['current_password'], $user->password)) {
                return $this->respondError(
                    'La contraseña actual es incorrecta',
                    422
                );
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            // Registrar auditoría
            $this->registrarAuditoria(
                $user->id,
                'USER',
                'UPDATE',
                $user->id,
                ['password' => '****'],
                ['password' => '****']
            );

            return $this->respondSuccess(
                null,
                'Contraseña actualizada correctamente'
            );

        } catch (\Exception $e) {
            return $this->respondInternalError('Error al cambiar contraseña', $e);
        }
    }

    /**
     * Registra una operación en auditoría
     * 
     * @param int $idUsuario
     * @param string $tabla
     * @param string $operacion
     * @param int $registroId
     * @param array|null $datosAnteriores
     * @param array|null $datosNuevos
     * @return void
     */
    private function registrarAuditoria(
        int $idUsuario,
        string $tabla,
        string $operacion,
        int $registroId,
        ?array $datosAnteriores,
        ?array $datosNuevos
    ): void {
        try {
            // Importar modelo Auditoria
            // \App\Models\Auditoria::create([
            //     'idUsuario' => $idUsuario,
            //     'tabla' => $tabla,
            //     'operacion' => $operacion,
            //     'registro_id' => $registroId,
            //     'datos_anteriores' => json_encode($datosAnteriores),
            //     'datos_nuevos' => json_encode($datosNuevos),
            //     'fecha' => now(),
            //     'ip' => request()->ip(),
            // ]);
        } catch (\Exception $e) {
            // No lanzar excepción, solo registrar en logs
            \Log::warning('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
