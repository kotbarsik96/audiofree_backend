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
        Schema::create('taxonomies_meta', function (Blueprint $table) {
            $table->id();
            $table->string('taxonomy_type');
            $table->string('taxonomy_name');
            $table->string('meta_name');
            $table->longText('meta_value');
            $table->timestamps();

            $table->foreign('taxonomy_type')
                ->references('type')
                ->on('taxonomies_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('taxonomy_name')
                ->references('name')
                ->on('taxonomies')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomies_meta');
    }
};
