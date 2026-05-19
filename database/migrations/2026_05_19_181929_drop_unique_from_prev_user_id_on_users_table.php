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
        Schema::table('users', function (Blueprint $table) {
            // Laravel automatically looks up 'users_prev_user_id_unique'
            // based on the column name inside the array
            $table->dropUnique(['prev_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-enforces uniqueness if you ever rollback
            $table->unique('prev_user_id');
        });
    }
};
