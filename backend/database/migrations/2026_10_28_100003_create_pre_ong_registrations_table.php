<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_ong_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone', 20)->index();
            $table->text('description')->nullable();
            $table->string('cnpj', 18)->nullable()->index();
            $table->string('city');
            $table->string('state', 2);
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('ong_id')->nullable()->constrained('ongs')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('deleted_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_ong_registrations');
    }
};
