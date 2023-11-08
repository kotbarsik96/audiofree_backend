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
            $table->foreignId('user_id')
                ->constrained(table: 'users')
                ->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('total_price');
            $table->boolean('is_oneclick');
            $table->integer('paid');
            $table->string('applied_coupon')
                ->nullable();
            $table->string('name')
                ->nullable();
            $table->string('email')
                ->nullable();
            $table->string('phone_number')
                ->nullable();
            $table->string('location')
                ->nullable();
            $table->text('comment')
                ->nullable();
            $table->string('address')
                ->nullable();
            $table->string('cart_rows'); // ссылаются на cart_product.id
            $table->foreignId('status_id')
                ->constrained(table: 'order_statuses')
                ->cascadeOnDelete();
            $table->foreignId('delivery_type')
                ->nullable()
                ->constrained(table: 'delivery_types')
                ->nullOnDelete();
            $table->foreignId('payment_type')
                ->nullable()
                ->constrained(table: 'payment_types')
                ->nullOnDelete();
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
