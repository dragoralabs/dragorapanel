<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fqdn')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->integer('port')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token', 255)->unique();
            $table->bigInteger('memory_mb')->nullable();
            $table->bigInteger('storage_mb')->nullable();
            $table->integer('cpu_cores')->default(0);
            $table->bigInteger('disk_used_mb')->default(0);
            $table->bigInteger('memory_used_mb')->default(0);
            $table->decimal('cpu_percent', 5, 1)->default(0.0);
            $table->string('status', 50)->default('offline');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->foreign('node_id')->references('id')->on('nodes')->nullOnDelete();
        });

        Schema::table('allocations', function (Blueprint $table) {
            $table->foreign('node_id')->references('id')->on('nodes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['node_id']);
            $table->dropColumn('node_id');
        });
        Schema::table('allocations', function (Blueprint $table) {
            $table->dropForeign(['node_id']);
            $table->dropColumn('node_id');
        });
        Schema::dropIfExists('nodes');
    }
};
