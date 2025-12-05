<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_stops', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique(); // teatro, puerto, santaCatalina
            $table->string('name');
            $table->string('zone')->nullable(); // TERMINALES, VEGUETA, TRIANA, etc.
            $table->decimal('lat_outbound', 10, 6);
            $table->decimal('lng_outbound', 10, 6);
            $table->decimal('lat_inbound', 10, 6);
            $table->decimal('lng_inbound', 10, 6);
            $table->timestamps();
            
            $table->index(['zone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_stops');
    }
};
