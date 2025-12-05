<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('bus_companies')->cascadeOnDelete();
            $table->string('line_number', 20); // 1, 12, L1, etc.
            $table->string('type', 20); // urban, interurban, night
            $table->string('origin');
            $table->string('destination');
            $table->string('color', 7)->nullable();
            $table->boolean('is_main_line')->default(false);
            $table->timestamps();
            
            $table->unique(['company_id', 'line_number']);
            $table->index(['type']);
            $table->index(['is_main_line']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_lines');
    }
};
