<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 64)->unique();
            $table->string('name', 128);
            $table->string('version', 32);
            $table->text('description')->nullable();
            $table->string('author', 128)->nullable();
            $table->string('license', 64)->nullable();
            $table->string('icon', 64)->default('fa-plug');
            $table->json('hooks')->nullable();
            $table->json('plugin_config')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
