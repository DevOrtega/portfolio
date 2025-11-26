<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Personal Info translations
        Schema::table('personal_infos', function (Blueprint $table) {
            $table->string('headline_en')->nullable()->after('headline');
            $table->text('bio_en')->nullable()->after('bio');
        });

        // Experience translations
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('role_en')->nullable()->after('role');
            $table->text('description_en')->nullable()->after('description');
        });

        // Projects translations
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
        });

        // Education translations
        Schema::table('education', function (Blueprint $table) {
            $table->string('degree_en')->nullable()->after('degree');
            $table->text('description_en')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            $table->dropColumn(['headline_en', 'bio_en']);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn(['role_en', 'description_en']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'description_en']);
        });

        Schema::table('education', function (Blueprint $table) {
            $table->dropColumn(['degree_en', 'description_en']);
        });
    }
};
