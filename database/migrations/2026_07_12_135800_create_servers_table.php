<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id')->nullable()->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type', 50)->default('minecraft');
            $table->string('version', 50)->default('1.21.4');
            $table->string('status', 50)->default('offline');
            $table->integer('memory_mb')->default(1024);
            $table->integer('storage_mb')->default(5120);
            $table->integer('port')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
