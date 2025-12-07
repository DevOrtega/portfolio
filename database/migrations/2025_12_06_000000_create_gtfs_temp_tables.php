<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('gtfs_routes');
        Schema::dropIfExists('gtfs_stops');
        Schema::dropIfExists('gtfs_stop_times');
        Schema::dropIfExists('gtfs_trips');

        Schema::create('gtfs_routes', function (Blueprint $table) {
            $table->string('route_id')->primary();
            $table->string('agency_id')->nullable();
            $table->string('route_short_name')->nullable();
            $table->string('route_long_name')->nullable();
            $table->string('route_color')->nullable();
        });
        Schema::create('gtfs_stops', function (Blueprint $table) {
            $table->string('stop_id')->primary();
            $table->string('stop_name')->nullable();
            $table->float('stop_lat')->nullable();
            $table->float('stop_lon')->nullable();
        });
        Schema::create('gtfs_stop_times', function (Blueprint $table) {
            $table->string('trip_id');
            $table->string('arrival_time')->nullable();
            $table->string('departure_time')->nullable();
            $table->string('stop_id');
            $table->integer('stop_sequence')->nullable();
        });
        Schema::create('gtfs_trips', function (Blueprint $table) {
            $table->string('route_id');
            $table->string('service_id')->nullable();
            $table->string('trip_id')->primary();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gtfs_routes');
        Schema::dropIfExists('gtfs_stops');
        Schema::dropIfExists('gtfs_stop_times');
        Schema::dropIfExists('gtfs_trips');
    }
};
