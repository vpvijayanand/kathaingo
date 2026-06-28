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
        Schema::create('metadata_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('name_en')->nullable();
            $table->boolean('is_hierarchical')->default(false);
            $table->timestamps();

            $table->unique(['category_id', 'slug']);
        });

        Schema::create('metadata_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metadata_type_id')->constrained('metadata_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('metadata_values')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('name_en')->nullable();
            $table->timestamps();

            $table->unique(['metadata_type_id', 'slug']);
        });

        Schema::create('post_metadata', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('metadata_value_id')->constrained('metadata_values')->onDelete('cascade');
            $table->primary(['post_id', 'metadata_value_id']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('name_en')->nullable();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->string('title_en')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->constrained('series')->onDelete('set null');
            $table->string('volume')->nullable();
            $table->integer('chapter_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn(['series_id', 'volume', 'chapter_number']);
        });

        Schema::dropIfExists('series');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_metadata');
        Schema::dropIfExists('metadata_values');
        Schema::dropIfExists('metadata_types');
    }
};
