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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('txn_id')->constrained('transactions');
            $table->foreignId('sender_id')->constrained('accounts');
            $table->foreignId('receiving_id')->constrained('accounts');
            $table->decimal('amount', 64, 0);
            $table->enum('type', ['deposit', 'withdraw']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
