<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_health_records', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('veterinarian')->nullable();
            $table->text('description')->nullable();
            $table->date('performed_at');
            $table->date('next_due_at')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->foreignId('health_record_type_id')->constrained('health_record_types');
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['pet_id', 'performed_at']);
            $table->index(['pet_id', 'health_record_type_id']);
            $table->index('next_due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_health_records');
    }
};
