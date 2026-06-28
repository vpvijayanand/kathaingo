<?php

namespace App\Services;

class CaptchaService
{
    protected $challenges = [
        [
            'question' => 'தமிழ் எழுத்து "அ" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "அ")',
            'target' => 'அ',
            'options' => ['அ', 'ஆ', 'இ', 'உ']
        ],
        [
            'question' => 'தமிழ் எழுத்து "க" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "க")',
            'target' => 'க',
            'options' => ['க', 'ங', 'ச', 'ஞ']
        ],
        [
            'question' => 'தமிழ் எழுத்து "ம" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "ம")',
            'target' => 'ம',
            'options' => ['ப', 'ம', 'ய', 'ர']
        ],
        [
            'question' => 'தமிழ் எழுத்து "வ" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "வ")',
            'target' => 'வ',
            'options' => ['ல', 'ள', 'ழ', 'வ']
        ],
        [
            'question' => 'தமிழ் எழுத்து "ஞ" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "ஞ")',
            'target' => 'ஞ',
            'options' => ['ஞ', 'ந', 'ண', 'ம']
        ],
        [
            'question' => 'தமிழ் எழுத்து "ஆ" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "ஆ")',
            'target' => 'ஆ',
            'options' => ['அ', 'ஆ', 'ஐ', 'ஔ']
        ],
        [
            'question' => 'தமிழ் எழுத்து "ச" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "ச")',
            'target' => 'ச',
            'options' => ['த', 'ந', 'ச', 'ஞ']
        ],
        [
            'question' => 'தமிழ் எழுத்து "ட" ஐத் தேர்ந்தெடுக்கவும் (Select Tamil letter "ட")',
            'target' => 'ட',
            'options' => ['ட', 'ண', 'த', 'ந']
        ]
    ];

    public function generate(): array
    {
        $index = array_rand($this->challenges);
        $challenge = $this->challenges[$index];

        // Shuffle options
        $options = $challenge['options'];
        shuffle($options);

        // Store correct answer in session
        session(['captcha_answer' => $challenge['target']]);

        return [
            'question' => $challenge['question'],
            'options' => $options
        ];
    }

    public function verify(?string $answer): bool
    {
        $correctAnswer = session('captcha_answer');
        if (empty($correctAnswer)) {
            return false;
        }

        // Clear answer after check to prevent replay attacks
        session()->forget('captcha_answer');

        return $answer === $correctAnswer;
    }
}
