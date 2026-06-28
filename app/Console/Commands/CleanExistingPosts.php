<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Services\ContentProcessorService;

class CleanExistingPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean spelling, grammar, punctuation, and typography spacing for all existing posts in the database';

    /**
     * Execute the console command.
     */
    public function handle(ContentProcessorService $processor)
    {
        $posts = Post::all();
        $this->info("Found {$posts->count()} posts to clean.");

        $updatedCount = 0;
        foreach ($posts as $post) {
            $originalContent = $post->content;
            $cleanedContent = $processor->process($originalContent);

            if ($originalContent !== $cleanedContent) {
                $post->content = $cleanedContent;
                $post->save();
                $this->line("Updated post #{$post->id}: '{$post->title}'");
                $updatedCount++;
            } else {
                $this->line("Post #{$post->id}: '{$post->title}' is already clean.");
            }
        }

        $this->info("Successfully cleaned {$updatedCount} posts.");
    }
}
