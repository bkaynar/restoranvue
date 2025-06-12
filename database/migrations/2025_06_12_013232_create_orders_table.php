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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained()->onDelete('cascade'); // Hangi masadan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Garson ID'si (siparişi kim aldı)
            $table->string('note')->nullable();
            $table->enum('status', ['hazırlanıyor', 'hazır', 'teslim', 'kapandı', 'ödendi', 'iptal'])->default('hazırlanıyor');
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamp('closed_at')->nullable(); // Ne zaman kapandı?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
