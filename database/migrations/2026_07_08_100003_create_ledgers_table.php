<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_type', 20);          // TransactionType enum value
            $table->uuid('account_id');
            $table->uuid('category_id')->nullable();
            $table->uuid('reference_id')->nullable();         // Links multi-row transfer entries
            $table->decimal('amount', 15, 2);                // Always positive
            $table->boolean('is_mutation_in');               // TRUE = credit, FALSE = debit
            $table->string('contact_name', 100)->nullable(); // Used for Debt/Loan types
            $table->timestamp('transaction_date');
            $table->text('note')->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->cascadeOnDelete();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete();

            $table->index('transaction_date');
            $table->index('transaction_type');
            $table->index('reference_id');
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
