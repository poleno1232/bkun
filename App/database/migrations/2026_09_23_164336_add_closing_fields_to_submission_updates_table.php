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
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_updates', function (Blueprint $table) {
            $table->dropColumn('is_closed');
            $table->dropColumn('closed_at');
        });
    }
};
