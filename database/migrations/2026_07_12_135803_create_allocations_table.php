<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->nullable()->index();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('ip', 45);
            $table->integer('port');
            $table->foreignId('server_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('assigned')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};
