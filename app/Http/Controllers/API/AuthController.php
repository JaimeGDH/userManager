<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Arr;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'new', 'me']]);
    }

    private function isEmailUniqueInExternalAPI($email)
    {
        $response = Http::get("https://64d25c51f8d60b174361f0a6.mockapi.io/users?email=$email");

        if ($response->successful()) {
            $users = $response->json();

            return count($users) === 0;
        } else {
            return false;
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        $email = $request->email;
        $password = $request->password;
        $credentials = $request->only('email', 'password');

        // Realizar una solicitud GET hacia mockapi para obtener el hash de contraseña
        $response = Http::get("https://64d25c51f8d60b174361f0a6.mockapi.io/users?email=$email");

        if ($response->successful()) {
            $users = $response->json();
            
            if (count($users) === 1 && Hash::check($password, $users[0]['password'])) {
                // Generar token JWT
                $tokenResponse = JWTAuth::attempt(['email' => $email, 'password' => $password]);
                unset($users[0]['password']);
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $users[0],
                    'access_token' => $tokenResponse,
                ]);
            } else {
                // Contraseña incorrecta
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Failed to retrieve user data from external API',
            ], 401);
        }
    }

    public function new(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $email = $request->email;

        // Realizar una consulta para verificar que el email sea único
        $response = Http::get("https://64d25c51f8d60b174361f0a6.mockapi.io/users?email=$email");

        if ($response->successful()) {
            $users = $response->json();

            // Verificar si el correo electrónico ya está registrado
            if (count($users) > 0) {
                return response()->json(['message' => 'Email already registered'], 400);
            }
        } else {
            return response()->json(['message' => 'Failed to check email uniqueness'], 500);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        
        $response = Http::post('https://64d25c51f8d60b174361f0a6.mockapi.io/users', $userData);
        
        if ($response->successful()) {
            return response()->json(['message' => 'Registration successful']);
        } else {
            return response()->json(['message' => 'Registration failed'], 500);
        }
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Verificar la autenticación usando el middleware auth:api
        if ($user->id !== auth()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $response = Http::delete("https://64d25c51f8d60b174361f0a6.mockapi.io/users/$id");

        if (!$response->successful()) {
            return response()->json(['message' => 'Failed to delete user from external API'], 500);
        }

        return response()->json(['message' => 'User account deleted successfully']);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me(Request $request, $id)
    {
        // Obtener el usuario autenticado
        $authenticatedUser = JWTAuth::parseToken()->authenticate();
        
        // Verificar si el usuario autenticado coincide con el ID proporcionado en la URL
        if ($authenticatedUser->id != $id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Validar los datos proporcionados en la solicitud
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $authenticatedUser->id,
            'password' => 'required|string|min:6',
        ]);

        // Obtener el valor del email del request
        $email = $request->email;

        // Construir los datos actualizados
        $updatedData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Si se proporcionó una nueva contraseña, hashearla y agregarla a los datos actualizados
        if ($request->has('password')) {
            $updatedData['password'] = Hash::make($request->password);
        }

        // Realizar la solicitud PATCH a la API externa para actualizar la información
        $response = Http::patch("https://64d25c51f8d60b174361f0a6.mockapi.io/users/$id", $updatedData);

        if ($response->successful()) {
            return response()->json(['message' => 'User information updated successfully']);
        } else {
            return response()->json(['message' => 'Failed to update user information'], 500);
        }
    }

    public function list() 
    {
        $response = Http::get('https://64d25c51f8d60b174361f0a6.mockapi.io/users/');

        if ($response->successful()) {
            $users = collect($response->json())->map(function ($user) {
                return Arr::except($user, ['password']);
            });

            return response()->json($users);
        } else {
            return response()->json(['message' => 'Failed to get users information'], 500);
        }
    }
}
