<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_vaccines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('batch')->nullable();
            $table->string('veterinarian')->nullable();
            $table->text('notes')->nullable();
            $table->date('applied_at');
            $table->date('next_dose_at')->nullable();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['pet_id', 'applied_at']);
            $table->index('next_dose_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_vaccines');
    }
};
