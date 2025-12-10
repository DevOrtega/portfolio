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
            $table->decimal('lat_outbound', 11, 8)->change();
            $table->decimal('lng_outbound', 11, 8)->change();
            $table->decimal('lat_inbound', 11, 8)->change();
            $table->decimal('lng_inbound', 11, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_stops', function (Blueprint $table) {
            $table->decimal('lat_outbound', 11, 8)->change();
            $table->decimal('lng_outbound', 11, 8)->change();
            $table->decimal('lat_inbound', 11, 8)->change();
            $table->decimal('lng_inbound', 11, 8)->change();
        });
    }
};
