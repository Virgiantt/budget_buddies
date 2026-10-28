<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Accounts
        Schema::create('Accounts', function (Blueprint $table) {
            $table->char('A_id', 6)->primary();
            $table->string('A_name', 100);
            $table->string('A_account_type', 50);
            $table->decimal('A_initial_balance', 12, 2)->default(0);

            // Menghubungkan akun ke pengguna Laravel
            $table->foreignId('Users_U_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('A_created_at')->nullable();
            $table->timestamp('A_updated_at')->nullable();
        });

        // 2. Categories
        Schema::create('Categories', function (Blueprint $table) {
            $table->char('C_id', 6)->primary();
            $table->string('C_name', 100);
            $table->string('C_type', 50);

            $table->timestamp('C_created_at')->nullable();
            $table->timestamp('C_updated_at')->nullable();
        });

        // 3. Transactions
        Schema::create('Transactions', function (Blueprint $table) {
            $table->char('T_ID', 6)->primary();
            $table->string('T_title', 255);
            $table->string('T_type', 50);
            $table->decimal('T_amount', 12, 2);
            $table->date('T_transaction_date');

            $table->char('Accounts_A_id', 6);
            $table->char('Categories_C_id', 6);

            $table->foreign('Accounts_A_id')
                ->references('A_id')
                ->on('Accounts')
                ->cascadeOnDelete();

            $table->foreign('Categories_C_id')
                ->references('C_id')
                ->on('Categories');

            $table->timestamp('T_created_at')->nullable();
            $table->timestamp('T_updated_at')->nullable();
        });

        // 4. Budgets
        Schema::create('Budgets', function (Blueprint $table) {
            $table->char('B_id', 6)->primary();
            $table->decimal('B_amount_limit', 12, 2);
            $table->unsignedInteger('B_month');
            $table->unsignedInteger('B_year');

            $table->char('Categories_C_id', 6);

            $table->foreign('Categories_C_id')
                ->references('C_id')
                ->on('Categories');

            $table->timestamp('B_created_at')->nullable();
            $table->timestamp('B_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Budgets');
        Schema::dropIfExists('Transactions');
        Schema::dropIfExists('Categories');
        Schema::dropIfExists('Accounts');
    }
};
