<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('post_reactions')
            ->where('reaction_type', 'insightful')
            ->update(['reaction_type' => 'agree']);

        DB::table('comment_reactions')
            ->where('reaction_type', 'insightful')
            ->update(['reaction_type' => 'agree']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('post_reactions')
            ->where('reaction_type', 'agree')
            ->update(['reaction_type' => 'insightful']);

        DB::table('comment_reactions')
            ->where('reaction_type', 'agree')
            ->update(['reaction_type' => 'insightful']);
    }
};
