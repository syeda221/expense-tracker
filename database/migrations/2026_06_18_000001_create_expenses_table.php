<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('description');
            $table->string('category');
            $table->string('merchant')->nullable();
            $table->string('payment_method');
            $table->date('expense_date');
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('expense_date');
            $table->index('merchant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
