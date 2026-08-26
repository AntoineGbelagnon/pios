<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payable_id')->nullable(); // polymorphic
            $table->string('payable_type')->nullable();
            $table->string('payment_number')->unique();
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer', 'credit', 'mixed']);
            $table->json('payment_details')->nullable(); // for mixed payments: [{method, amount}]
            $table->enum('direction', ['in', 'out']); // in=customer pays, out=we pay supplier
            $table->string('reference')->nullable(); // invoice/transaction reference
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'created_at']);
            $table->index(['company_id', 'payable_type', 'payable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
