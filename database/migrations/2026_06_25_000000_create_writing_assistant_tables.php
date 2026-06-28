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
        Schema::create('writing_assistant_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('language'); // 'ta', 'en'
            
            $table->unique(['word', 'language']);
            $table->index(['word', 'language']);
        });

        Schema::create('personal_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('word');
            $table->string('language');
            $table->timestamps();

            $table->unique(['user_id', 'word', 'language']);
        });

        Schema::create('community_suggested_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('language');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->integer('nominations_count')->default(1);
            $table->timestamps();

            $table->unique(['word', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_suggested_words');
        Schema::dropIfExists('personal_dictionaries');
        Schema::dropIfExists('writing_assistant_words');
    }
};
