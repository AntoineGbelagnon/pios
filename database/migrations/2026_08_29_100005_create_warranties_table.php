<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('warranty_number')->unique();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date');
            $table->integer('duration_months');
            $table->date('expiry_date');
            $table->string('problem_description')->nullable();
            $table->enum('status', ['active', 'expired', 'repaired', 'replaced', 'closed'])->default('active');
            $table->text('resolution_notes')->nullable();
            $table->decimal('repair_cost', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
