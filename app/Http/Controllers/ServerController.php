<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServerController extends Controller
{
    private function getUser(Request $request): ?\App\Models\User
    {
        $header = $request->header('Authorization', '');
        if (!preg_match('/Bearer\s+(.+)$/i', $header, $m)) {
            return null;
        }
        $session = Session::where('token', $m[1])->valid()->with('user')->first();
        return $session?->user;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $cacheKey = 'servers:list:v' . Cache::get('servers:version', 1) . ':' . ($user->isAdmin() ? 'admin' : 'user:' . $user->id);
        $servers = Cache::remember($cacheKey, 5, function () use ($user) {
            if ($user->isAdmin()) {
                return Server::with('user')->orderBy('created_at', 'desc')->get()->map(function ($s) {
                    $data = $s->toArray();
                    $data['user_email'] = $s->user->email ?? null;
                    return $data;
                });
            }
            return Server::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        });

        return response()->json(['success' => true, 'servers' => $servers]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $server = Server::find($id);
        if (!$server || (!$user->isAdmin() && $server->user_id !== $user->id)) {
            return response()->json(['success' => false, 'error' => 'Server not found.'], 404);
        }

        $data = $server->toArray();
        if ($server->node_id) {
            $node = $server->node;
            if ($node) {
                $host = $node->fqdn ?: $node->ip_address ?: 'localhost';
                $data['agent_url'] = "http://{$host}:{$node->port}";
                $data['agent_token'] = $node->token;
            }
        }
        return response()->json(['success' => true, 'server' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'string|max:50',
            'version' => 'string|max:50',
            'memory_mb' => 'integer|min:256',
            'storage_mb' => 'integer|min:1024',
            'port' => 'integer|nullable',
        ];
        if ($user->isAdmin()) {
            $rules['user_id'] = 'required|integer|exists:users,id';
            $rules['node_id'] = 'nullable|integer|exists:nodes,id';
        }
        $data = $request->validate($rules);

        Cache::increment('servers:version');

        $server = Server::create([
            'user_id' => $user->isAdmin() ? $data['user_id'] : $user->id,
            'node_id' => $user->isAdmin() ? ($data['node_id'] ?? null) : null,
            'name' => $data['name'],
            'type' => $data['type'] ?? 'minecraft',
            'version' => $data['version'] ?? '1.21.4',
            'memory_mb' => (int)($data['memory_mb'] ?? 1024),
            'storage_mb' => (int)($data['storage_mb'] ?? 5120),
            'port' => $data['port'] ?? null,
        ]);

        return response()->json(['success' => true, 'server' => $server]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $server = Server::find($id);
        if (!$server || (!$user->isAdmin() && $server->user_id !== $user->id)) {
            return response()->json(['success' => false, 'error' => 'Server not found.'], 404);
        }

        $allowed = ['name', 'type', 'version', 'status', 'memory_mb', 'storage_mb', 'port', 'ip_address', 'user_id', 'node_id'];
        $data = $request->only($allowed);
        if ($user->isAdmin()) {
            // allow user_id and node_id changes for admins
        } else {
            unset($data['user_id'], $data['node_id']);
        }
        $data = array_filter($data, fn($v) => $v !== null);

        if (empty($data)) {
            return response()->json(['success' => false, 'error' => 'No valid fields to update.'], 400);
        }

        $server->update($data);
        $server->refresh();
        Cache::increment('servers:version');

        return response()->json(['success' => true, 'server' => $server]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser($request);
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Not authenticated.'], 401);
        }

        $server = Server::find($id);
        if (!$server || (!$user->isAdmin() && $server->user_id !== $user->id)) {
            return response()->json(['success' => false, 'error' => 'Server not found.'], 404);
        }

        $server->delete();
        Cache::increment('servers:version');

        return response()->json(['success' => true]);
    }
}
