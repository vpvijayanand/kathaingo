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
        Schema::table('subcategories', function (Blueprint $table) {
            $table->unsignedInteger('trust_level')->default(1)->after('user_id');
            $table->string('tagline')->nullable()->after('topics');
            $table->string('tagline_en')->nullable()->after('tagline');
            $table->boolean('is_featured')->default(false)->after('tagline_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['trust_level', 'tagline', 'tagline_en', 'is_featured']);
        });
    }
};
