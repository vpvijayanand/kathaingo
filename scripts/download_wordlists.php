<?php

$dir = __DIR__ . '/../storage/app/writing_assistant';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$sources = [
    'tamil' => [
        'url' => 'https://raw.githubusercontent.com/KaniyamFoundation/all_tamil_nouns/master/unique_sorted_noun_master.txt',
        'fallback' => 'https://raw.githubusercontent.com/KaniyamFoundation/all_tamil_nouns/main/unique_sorted_noun_master.txt',
        'file' => $dir . '/tamil_words.txt'
    ],
    'english' => [
        'url' => 'https://raw.githubusercontent.com/first20hours/google-10000-english/master/google-10000-english-usa-no-swears.txt',
        'file' => $dir . '/english_words.txt'
    ]
];

foreach ($sources as $lang => $info) {
    echo "Downloading {$lang} words list...\n";
    $content = @file_get_contents($info['url']);
    if ($content === false && isset($info['fallback'])) {
        echo "Trying fallback URL for {$lang}...\n";
        $content = @file_get_contents($info['fallback']);
    }
    
    if ($content !== false) {
        file_put_contents($info['file'], $content);
        echo "Saved to " . realpath($info['file']) . "\n";
    } else {
        echo "Failed to download {$lang} list!\n";
    }
}
