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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->unsignedInteger('discount_price')->nullable();
            $table->text('description')
                ->nullable();
            $table->unsignedInteger('quantity')
                ->nullable();
            $table->string('product_status')
                ->nullable();
            $table->string('brand')
                ->nullable();
            $table->string('category')
                ->nullable();
            $table->string('type')
                ->nullable();
            $table->foreignId('image_id')
                ->nullable()
                ->constrained(table: 'images')
                ->nullOnDelete();
            $table->timestamps();

            $table->foreign('product_status')->references('name')->on('taxonomies')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('brand')->references('name')->on('taxonomies')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('category')->references('name')->on('taxonomies')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('type')->references('name')->on('taxonomies')
                ->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
