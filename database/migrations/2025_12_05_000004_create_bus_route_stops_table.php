<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained('bus_lines')->cascadeOnDelete();
            $table->foreignId('stop_id')->constrained('bus_stops')->cascadeOnDelete();
            $table->enum('direction', ['outbound', 'inbound']);
            $table->unsignedSmallInteger('order'); // Order in the route
            $table->timestamps();
            
            $table->unique(['line_id', 'stop_id', 'direction', 'order']);
            $table->index(['line_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_route_stops');
    }
};
