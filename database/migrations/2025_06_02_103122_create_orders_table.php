<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('delivery_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('from_address')->nullable();
            $table->text('to_address')->nullable();
            $table->datetime('delivery_time')->nullable();
            $table->string('otp')->unique();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
