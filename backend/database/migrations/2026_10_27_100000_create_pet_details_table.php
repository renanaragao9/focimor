<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_details', function (Blueprint $table) {
            $table->id();
            $table->string('microchip')->nullable()->unique();
            $table->string('pedigree_number')->nullable();
            $table->string('coat')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('temperament')->nullable();
            $table->string('vet_name')->nullable();
            $table->string('vet_clinic')->nullable();
            $table->string('vet_phone')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('diet')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('pet_id')->unique()->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_details');
    }
};
