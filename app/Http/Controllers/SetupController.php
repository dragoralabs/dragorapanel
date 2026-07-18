<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Server;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function seed(): JsonResponse
    {
        $user = User::where('email', 'admin@hostit.local')->first();

        if (!$user) {
            $user = User::create([
                'email' => 'admin@hostit.local',
                'password' => Hash::make('admin123'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
            ]);

            $servers = [
                ['user_id' => $user->id, 'name' => 'MC-Survival', 'type' => 'minecraft', 'version' => '1.21.4', 'status' => 'online', 'memory_mb' => 2048, 'storage_mb' => 10240, 'port' => 25565, 'ip_address' => '192.168.1.10'],
                ['user_id' => $user->id, 'name' => 'MC-Hardcore', 'type' => 'minecraft', 'version' => '1.21.4', 'status' => 'online', 'memory_mb' => 2048, 'storage_mb' => 8192, 'port' => 25566, 'ip_address' => '192.168.1.11'],
                ['user_id' => $user->id, 'name' => 'MC-Creative', 'type' => 'minecraft', 'version' => '1.21.4', 'status' => 'online', 'memory_mb' => 1024, 'storage_mb' => 5120, 'port' => 25567, 'ip_address' => '192.168.1.12'],
                ['user_id' => $user->id, 'name' => 'MC-Survival-2', 'type' => 'minecraft', 'version' => '1.20.4', 'status' => 'offline', 'memory_mb' => 1024, 'storage_mb' => 5120, 'port' => 25568, 'ip_address' => '192.168.1.13'],
            ];

            foreach ($servers as $s) {
                Server::create($s);
            }
        }

        // Seed default location if none exist
        if (Location::count() === 0) {
            Location::create(['short_code' => 'US-EAST', 'long_name' => 'US East Coast', 'description' => 'Primary data center location']);
        }

        // Seed default settings
        if (Setting::count() === 0) {
            Setting::set('panel:name', 'DragoraPanel');
            Setting::set('panel:locale', 'en');
            Setting::set('theme:default', 'dark');
        }

        return response()->json(['success' => true, 'message' => 'Database ready.']);
    }
}
