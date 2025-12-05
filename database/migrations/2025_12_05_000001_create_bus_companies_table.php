<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_companies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // municipales, global, night
            $table->string('name');
            $table->string('primary_color', 7); // #FDB913
            $table->string('secondary_color', 7);
            $table->string('text_color', 7)->default('#333');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_companies');
    }
};
