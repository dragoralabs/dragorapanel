<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        return match ($request->query('action')) {
            'register' => $this->register($request),
            'login'    => $this->login($request),
            'logout'   => $this->logout($request),
            'me'       => $this->me($request),
            default    => response()->json(['success' => false, 'error' => 'Invalid action.'], 400),
        };
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);

        $token = $this->createSession($user->id);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email or password.',
            ], 401);
        }

        $token = $this->createSession($user->id);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $token = $this->getBearerToken($request);
        if (!$token) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $session = Session::where('token', $token)->valid()->with('user')->first();
        if (!$session) {
            return response()->json(['success' => false, 'error' => 'Invalid or expired token.'], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $this->formatUser($session->user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $this->getBearerToken($request);
        if ($token) {
            Session::where('token', $token)->delete();
        }

        return response()->json(['success' => true]);
    }

    private function createSession(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        Session::create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => now()->addDays(30),
        ]);
        return $token;
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
        ];
    }

    private function getBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');
        if (preg_match('/Bearer\s+(.+)$/i', $header, $m)) {
            return $m[1];
        }
        return null;
    }
}
