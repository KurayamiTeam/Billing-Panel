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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('driver')->default('pterodactyl');
            $table->integer('external_id')->unique();
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('status')->default('active');
            $table->integer('cpu');
            $table->integer('ram');
            $table->integer('disk');
            $table->date('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};