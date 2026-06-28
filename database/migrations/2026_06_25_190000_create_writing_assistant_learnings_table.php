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
        Schema::create('writing_assistant_learnings', function (Blueprint $table) {
            $table->id();
            $table->string('original_text');
            $table->string('corrected_text');
            $table->string('language', 10);
            $table->integer('frequency')->default(1);
            $table->integer('writer_trust_level')->default(1);
            $table->string('approval_status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->timestamps();

            $table->unique(['original_text', 'corrected_text', 'language'], 'wa_learnings_unique');
            $table->index(['original_text', 'language'], 'wa_learnings_original_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_assistant_learnings');
    }
};
