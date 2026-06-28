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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('visitor')->after('is_approved');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('content');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('excerpt');
        });
    }
};
