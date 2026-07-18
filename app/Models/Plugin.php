<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'unique_id', 'name', 'version', 'description', 'author',
        'license', 'icon', 'hooks', 'plugin_config', 'enabled',
    ];

    protected function casts(): array
    {
        return [
            'hooks' => 'array',
            'plugin_config' => 'array',
            'enabled' => 'boolean',
        ];
    }

    public function storagePath(): string
    {
        return storage_path('app/plugins/' . $this->unique_id);
    }

    public function assetUrl(string $path): string
    {
        return '/api/panel/plugins/assets/' . $this->unique_id . '/' . ltrim($path, '/');
    }
}
