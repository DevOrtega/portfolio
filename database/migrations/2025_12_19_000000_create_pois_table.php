<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pois', function (Blueprint $table) {
            $table->id(); // Internal ID
            $table->unsignedBigInteger('osm_id');
            $table->string('osm_type'); // node, way, relation
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->string('category');
            $table->string('name')->nullable();
            $table->json('tags')->nullable();
            $table->integer('relevance')->default(0);
            $table->timestamps();

            // Compound index for bounding box queries
            $table->index(['lat', 'lon']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pois');
    }
};
