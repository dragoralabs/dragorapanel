<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->integer('memory_used_mb')->nullable()->after('command_queue');
            $table->decimal('cpu_percent', 5, 1)->nullable()->after('memory_used_mb');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn(['memory_used_mb', 'cpu_percent']);
        });
    }
};
