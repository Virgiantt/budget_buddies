<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Categories', function (Blueprint $table) {
            $table->foreignId('Users_U_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });

        Schema::table('Budgets', function (Blueprint $table) {
            $table->foreignId('Users_U_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('Budgets', function (Blueprint $table) {
            $table->dropForeign(['Users_U_id']);
            $table->dropColumn('Users_U_id');
        });

        Schema::table('Categories', function (Blueprint $table) {
            $table->dropForeign(['Users_U_id']);
            $table->dropColumn('Users_U_id');
        });
    }
};