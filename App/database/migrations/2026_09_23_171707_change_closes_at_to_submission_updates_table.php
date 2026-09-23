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
        Schema::table('submission_updates', function (Blueprint $table) {
            $table->renameColumn('closed_at', 'closes_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_updates', function (Blueprint $table) {
            $table->renameColumn('closes_at', 'closed_at');
        });
    }
};
