<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vote_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained('votes');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->ipAddress('ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_user');
    }
};
