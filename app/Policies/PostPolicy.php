<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isAuthor() || $user->isSeoManager();
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        if ($user->isAdmin() || $user->isEditor() || $user->isSeoManager()) {
            return true;
        }

        return $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor() || $user->isAuthor();
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isSeoManager()) {
            return $post->status === 'published';
        }

        if ($user->isAuthor() && $user->id === $post->author_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        return $user->isAuthor() && $user->id === $post->author_id;
    }

    /**
     * Determine whether the user can change status of the post.
     */
    public function changeStatus(User $user, Post $post, string $targetStatus): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isAuthor() && $user->id === $post->author_id) {
            $trustLevel = $user->authorProfile->trust_level ?? 1;

            if ($trustLevel >= 3) {
                return in_array($targetStatus, ['draft', 'published']);
            }
            if ($trustLevel == 2) {
                return in_array($targetStatus, ['draft', 'submitted', 'published']);
            }
            return in_array($targetStatus, ['draft', 'submitted']);
        }

        return false;
    }
}
