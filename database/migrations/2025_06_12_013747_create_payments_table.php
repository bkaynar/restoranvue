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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->enum('payment_method', ['nakit', 'kredi_kartı', 'online', 'fiş', 'diğer'])->default('nakit');
            $table->enum('status', ['beklemede', 'ödendi', 'iptal'])->default('beklemede');
            $table->string('note')->nullable();
            $table->timestamp('paid_at')->nullable(); // Ne zaman ödendi?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
