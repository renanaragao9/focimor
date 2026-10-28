<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ong_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('status', 20)->default('active');
            $table->text('description')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->decimal('goal_amount', 12, 2);
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index(['ong_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ong_campaigns');
    }
};
