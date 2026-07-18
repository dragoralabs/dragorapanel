<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\Schedule;
use App\Models\ScheduleTask;
use App\Models\Server;
use App\Models\ServerDatabase;
use App\Models\Session;
use App\Models\Subuser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServerDetailController extends Controller
{
    private function getUser(Request $request): ?User
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        return Session::where('token', $token)->valid()->first()?->user;
    }

    private function getServer(Request $request, int $id): ?Server
    {
        $user = $this->getUser($request);
        if (!$user) return null;
        $server = Server::find($id);
        if (!$server) return null;
        if ($user->isAdmin()) return $server;
        if ($server->user_id === $user->id) return $server;
        // check subuser
        $subuser = Subuser::where('user_id', $user->id)->where('server_id', $id)->first();
        if ($subuser) return $server;
        return null;
    }

    private function checkAccess(Request $request, int $id): ?Server
    {
        $server = $this->getServer($request, $id);
        if (!$server) return null;
        return $server;
    }

    private function checkSubuserPerm(Server $server, User $user, string $perm): bool
    {
        if ($user->isAdmin() || $server->user_id === $user->id) return true;
        $subuser = Subuser::where('user_id', $user->id)->where('server_id', $server->id)->first();
        return $subuser && $subuser->hasPermission($perm);
    }

    // ── Console ──

    public function consoleSend(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'console')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['command' => 'required|string']);
        $server->queueCommand($data['command']);
        $server->appendLog('[' . now()->format('H:i:s') . '] [CMD] ' . $data['command']);
        ActivityLog::create(['server_id' => $server->id, 'user_id' => $user->id, 'action' => 'server:console', 'metadata' => json_encode(['command' => $data['command']]), 'ip_address' => $request->ip()]);
        return response()->json(['success' => true]);
    }

    public function consoleLogs(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        // Lightweight: only fetch console_logs column
        $raw = DB::table('servers')->where('id', $id)->value('console_logs');
        $logs = $raw ? explode("\n", $raw) : [];
        return response()->json(['success' => true, 'logs' => array_slice($logs, -200)]);
    }

    public function powerAction(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'console')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['action' => 'required|string|in:start,stop,restart']);
        $server->queueCommand('power:' . $data['action']);
        $server->appendLog('[' . now()->format('H:i:s') . '] [INFO] Power action: ' . $data['action']);
        ActivityLog::create(['server_id' => $server->id, 'user_id' => $user->id, 'action' => 'server:power', 'metadata' => json_encode(['action' => $data['action']]), 'ip_address' => $request->ip()]);
        return response()->json(['success' => true, 'action' => $data['action']]);
    }

    // ── File Manager ──

    public function filesList(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $path = $request->query('path', '/');
        $root = base_path("node_agent/servers/{$server->id}");
        $full = $this->resolvePath($root, $path);
        if (!$full || !is_dir($full)) return response()->json(['success' => false, 'error' => 'Invalid path.'], 400);

        $items = [];
        foreach (scandir($full) as $name) {
            if ($name === '.' || $name === '..') continue;
            $fp = $full . DIRECTORY_SEPARATOR . $name;
            $items[] = [
                'name' => $name,
                'type' => is_dir($fp) ? 'dir' : 'file',
                'size' => is_file($fp) ? filesize($fp) : 0,
                'last_modified' => filemtime($fp),
            ];
        }
        return response()->json(['success' => true, 'items' => $items, 'path' => $path]);
    }

    public function filesCreateDir(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string', 'name' => 'required|string']);
        $root = base_path("node_agent/servers/{$server->id}");
        $parent = $this->resolvePath($root, $data['path']);
        if (!$parent || !is_dir($parent)) return response()->json(['success' => false, 'error' => 'Invalid path.'], 400);
        $target = $parent . DIRECTORY_SEPARATOR . basename($data['name']);
        if (file_exists($target)) return response()->json(['success' => false, 'error' => 'Already exists.'], 400);
        mkdir($target, 0755, true);
        return response()->json(['success' => true]);
    }

    public function filesCreateFile(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string', 'name' => 'required|string', 'content' => 'nullable|string']);
        $root = base_path("node_agent/servers/{$server->id}");
        $parent = $this->resolvePath($root, $data['path']);
        if (!$parent || !is_dir($parent)) return response()->json(['success' => false, 'error' => 'Invalid path.'], 400);
        $target = $parent . DIRECTORY_SEPARATOR . basename($data['name']);
        file_put_contents($target, $data['content'] ?? '');
        return response()->json(['success' => true]);
    }

    public function filesRead(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $path = $request->query('path', '/');
        $root = base_path("node_agent/servers/{$server->id}");
        $full = $this->resolvePath($root, $path);
        if (!$full || !is_file($full)) return response()->json(['success' => false, 'error' => 'Invalid file.'], 400);
        return response()->json(['success' => true, 'content' => file_get_contents($full)]);
    }

    public function filesWrite(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string', 'content' => 'required|string']);
        $root = base_path("node_agent/servers/{$server->id}");
        $full = $this->resolvePath($root, $data['path']);
        if (!$full || !is_file($full)) return response()->json(['success' => false, 'error' => 'Invalid file.'], 400);
        file_put_contents($full, $data['content']);
        return response()->json(['success' => true]);
    }

    public function filesRename(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string', 'new_name' => 'required|string']);
        $root = base_path("node_agent/servers/{$server->id}");
        $full = $this->resolvePath($root, $data['path']);
        if (!$full || !file_exists($full)) return response()->json(['success' => false, 'error' => 'Not found.'], 400);
        $new = dirname($full) . DIRECTORY_SEPARATOR . basename($data['new_name']);
        if (file_exists($new)) return response()->json(['success' => false, 'error' => 'Target exists.'], 400);
        rename($full, $new);
        return response()->json(['success' => true]);
    }

    public function filesDelete(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string']);
        $root = base_path("node_agent/servers/{$server->id}");
        $full = $this->resolvePath($root, $data['path']);
        if (!$full || !file_exists($full)) return response()->json(['success' => false, 'error' => 'Not found.'], 400);
        if (is_dir($full)) {
            $this->rmdirRecursive($full);
        } else {
            unlink($full);
        }
        return response()->json(['success' => true]);
    }

    public function filesUpload(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'files')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['path' => 'required|string', 'file' => 'required|file|max:102400']);
        $root = base_path("node_agent/servers/{$server->id}");
        $parent = $this->resolvePath($root, $data['path']);
        if (!$parent || !is_dir($parent)) return response()->json(['success' => false, 'error' => 'Invalid path.'], 400);
        $request->file('file')->move($parent, $request->file('file')->getClientOriginalName());
        return response()->json(['success' => true]);
    }

    // ── Backups ──

    public function backupsIndex(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'backups')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        return response()->json(['success' => true, 'backups' => $server->backups()->orderBy('id', 'desc')->get()]);
    }

    public function backupsStore(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'backups')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['name' => 'required|string']);
        $backup = Backup::create(['server_id' => $server->id, 'name' => $data['name'], 'status' => 'creating']);
        // Queue backup job (would be done by node agent)
        $backup->update(['status' => 'completed', 'size_bytes' => 0]);
        ActivityLog::create(['server_id' => $server->id, 'user_id' => $user->id, 'action' => 'server:backup:create', 'metadata' => json_encode(['backup_id' => $backup->id, 'name' => $data['name']]), 'ip_address' => $request->ip()]);
        return response()->json(['success' => true, 'backup' => $backup]);
    }

    public function backupsDestroy(Request $request, int $id, Backup $backup): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $backup->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'backups')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        if ($backup->is_locked) return response()->json(['success' => false, 'error' => 'Backup is locked.'], 400);
        $backup->delete();
        return response()->json(['success' => true]);
    }

    public function backupsLock(Request $request, int $id, Backup $backup): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $backup->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $backup->update(['is_locked' => !$backup->is_locked]);
        return response()->json(['success' => true, 'is_locked' => $backup->fresh()->is_locked]);
    }

    // ── Databases ──

    public function databasesIndex(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'databases')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        return response()->json(['success' => true, 'databases' => $server->databases()->get()]);
    }

    public function databasesStore(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'databases')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate([
            'database_name' => 'required|string',
            'remote_host' => 'nullable|string',
            'password' => 'required|string|min:8',
        ]);
        $data['server_id'] = $server->id;
        $data['username'] = "s{$server->id}_" . Str::random(8);
        $data['remote_host'] ??= '%';
        $db = ServerDatabase::create($data);
        return response()->json(['success' => true, 'database' => $db->makeHidden('password')]);
    }

    public function databasesDestroy(Request $request, int $id, ServerDatabase $database): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $database->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'databases')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $database->delete();
        return response()->json(['success' => true]);
    }

    public function databasesResetPassword(Request $request, int $id, ServerDatabase $database): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $database->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'databases')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $newPass = Str::random(16);
        $database->update(['password' => $newPass]);
        return response()->json(['success' => true, 'password' => $newPass]);
    }

    // ── Schedules ──

    public function schedulesIndex(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'schedules')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        return response()->json(['success' => true, 'schedules' => $server->schedules()->with('tasks')->get()]);
    }

    public function schedulesStore(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'schedules')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate([
            'name' => 'required|string',
            'cron_minute' => 'required|string',
            'cron_hour' => 'required|string',
            'cron_day_of_week' => 'required|string',
            'cron_day_of_month' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['server_id'] = $server->id;
        $schedule = Schedule::create($data);

        // Create tasks if provided
        if ($request->has('tasks')) {
            foreach ($request->input('tasks', []) as $i => $task) {
                ScheduleTask::create([
                    'schedule_id' => $schedule->id,
                    'sequence_id' => $i + 1,
                    'action' => $task['action'] ?? 'command',
                    'payload' => $task['payload'] ?? null,
                    'time_offset' => $task['time_offset'] ?? 0,
                ]);
            }
        }
        return response()->json(['success' => true, 'schedule' => $schedule->load('tasks')]);
    }

    public function schedulesUpdate(Request $request, int $id, Schedule $schedule): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $schedule->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'schedules')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate([
            'name' => 'nullable|string',
            'cron_minute' => 'nullable|string',
            'cron_hour' => 'nullable|string',
            'cron_day_of_week' => 'nullable|string',
            'cron_day_of_month' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $schedule->update(array_filter($data));
        return response()->json(['success' => true, 'schedule' => $schedule->fresh()->load('tasks')]);
    }

    public function schedulesDestroy(Request $request, int $id, Schedule $schedule): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $schedule->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $schedule->delete();
        return response()->json(['success' => true]);
    }

    // ── Subusers ──

    public function subusersIndex(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        return response()->json(['success' => true, 'subusers' => $server->subusers()->with('user:id,email,first_name,last_name')->get()]);
    }

    public function subusersStore(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'subusers')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'permissions' => 'required|array',
        ]);
        $targetUser = User::where('email', $data['email'])->first();
        if ($targetUser->id === $server->user_id) return response()->json(['success' => false, 'error' => 'Cannot add owner as subuser.'], 400);
        if (Subuser::where('user_id', $targetUser->id)->where('server_id', $server->id)->exists()) return response()->json(['success' => false, 'error' => 'Already a subuser.'], 400);

        $subuser = Subuser::create([
            'user_id' => $targetUser->id,
            'server_id' => $server->id,
            'permissions' => json_encode($data['permissions']),
        ]);
        return response()->json(['success' => true, 'subuser' => $subuser->load('user:id,email,first_name,last_name')]);
    }

    public function subusersUpdate(Request $request, int $id, Subuser $subuser): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $subuser->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $user = $this->getUser($request);
        if (!$this->checkSubuserPerm($server, $user, 'subusers')) return response()->json(['success' => false, 'error' => 'No permission.'], 403);

        $data = $request->validate(['permissions' => 'required|array']);
        $subuser->update(['permissions' => json_encode($data['permissions'])]);
        return response()->json(['success' => true, 'subuser' => $subuser->fresh()->load('user:id,email,first_name,last_name')]);
    }

    public function subusersDestroy(Request $request, int $id, Subuser $subuser): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server || $subuser->server_id !== $server->id) return response()->json(['success' => false, 'error' => 'Not found.'], 404);
        $subuser->delete();
        return response()->json(['success' => true]);
    }

    // ── Activity per server ──

    public function activityIndex(Request $request, int $id): JsonResponse
    {
        $server = $this->checkAccess($request, $id);
        if (!$server) return response()->json(['success' => false, 'error' => 'Not found or access denied.'], 404);
        $logs = ActivityLog::where('server_id', $server->id)->with('user')->latest()->limit(50)->get();
        return response()->json(['success' => true, 'logs' => $logs]);
    }

    // ── Helpers ──

    private function resolvePath(string $root, string $path): ?string
    {
        $root = rtrim($root, '\\/');
        $path = str_replace(['../', '..\\'], '', $path);
        $full = realpath($root . DIRECTORY_SEPARATOR . ltrim($path, '\\/'));
        if ($full === false || !str_starts_with($full, $root)) return null;
        return $full;
    }

    private function rmdirRecursive(string $dir): void
    {
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->rmdirRecursive($path) : unlink($path);
        }
        rmdir($dir);
    }
}
