<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_weights', function (Blueprint $table) {
            $table->id();
            $table->date('measured_at');
            $table->decimal('weight_kg', 6, 2);
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('width_cm', 6, 2)->nullable();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['pet_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_weights');
    }
};
