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
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('smiley_count')->default(0);
            $table->unsignedInteger('thumbs_up_count')->default(0);
            $table->unsignedInteger('thumbs_down_count')->default(0);
            $table->unsignedInteger('angry_count')->default(0);
            $table->unsignedInteger('crying_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['smiley_count', 'thumbs_up_count', 'thumbs_down_count', 'angry_count', 'crying_count']);
        });
    }
};
