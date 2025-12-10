<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bus_stops', function (Blueprint $table) {
            $table->index(['lat_outbound', 'lng_outbound'])->name('idx_bus_stops_coords_outbound');
            $table->index(['lat_inbound', 'lng_inbound'])->name('idx_bus_stops_coords_inbound');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_stops', function (Blueprint $table) {
            $table->index(['lat_outbound', 'lng_outbound'])->name('idx_bus_stops_coords_outbound');
            $table->index(['lat_inbound', 'lng_inbound'])->name('idx_bus_stops_coords_inbound');
        });
    }
};
