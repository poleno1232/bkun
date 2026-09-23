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
        Schema::create('vote_updates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('chapter_id')->constrained('chapters');
            $table->timestamp('posted_at');
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_updates');
    }
};
