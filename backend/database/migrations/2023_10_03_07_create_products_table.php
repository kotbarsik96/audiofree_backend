<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->foreignId('type_id')
                ->nullable()
                ->constrained(table: 'types') // наушники, чехлы...
                ->nullOnDelete();
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained(table: 'brands')
                ->nullOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained(table: 'categories') // внутриканальные, вкладыши, ...
                ->nullOnDelete();
            $table->foreignId('image_id')
                ->nullable()
                ->constrained(table: 'images')
                ->nullOnDelete();
            $table->timestamps();
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