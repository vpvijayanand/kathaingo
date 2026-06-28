<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WritingAssistantLearning extends Model
{
    protected $table = 'writing_assistant_learnings';

    protected $fillable = [
        'original_text',
        'corrected_text',
        'language',
        'frequency',
        'writer_trust_level',
        'approval_status'
    ];
}
