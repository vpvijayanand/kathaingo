<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ContentProcessorService;
use Illuminate\Support\Facades\Http;

class ContentProcessorServiceTest extends TestCase
{
    private ContentProcessorService $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new ContentProcessorService();
    }

    public function test_plain_text_is_wrapped_in_paragraphs()
    {
        $text = "First paragraph.\n\nSecond paragraph.";
        $expected = "<p>First paragraph.</p>\n<p>Second paragraph.</p>";
        $this->assertEquals($expected, trim($this->processor->process($text)));
    }

    public function test_spacing_cleanup_collapses_multiple_spaces()
    {
        $text = "<p>Hello    world.  This is a   test.</p>";
        $expected = "<p>Hello world. This is a test.</p>";
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_punctuation_spacing()
    {
        // Spacing before and after punctuation
        $text = "<p>Hello ,world .How are you ? Fine !</p>";
        $expected = "<p>Hello, world. How are you? Fine!</p>";
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_decimal_numbers_and_multiple_punctuation_marks_are_preserved()
    {
        $text = "<p>Pi is 3.14. Wait... what??? Yes!!!</p>";
        $expected = "<p>Pi is 3.14. Wait... what??? Yes!!!</p>";
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_html_tags_and_attributes_are_preserved()
    {
        // Spacing cleanups should not break class names, image URLs, etc.
        $text = '<p>Check <a href="https://example.com/some,url.html" class="link-item">this link</a>.</p>';
        $expected = '<p>Check <a href="https://example.com/some,url.html" class="link-item">this link</a>.</p>';
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_english_spelling_correction_using_mock_api()
    {
        Http::fake([
            'api.languagetool.org/*' => Http::response([
                'matches' => [
                    [
                        'message' => 'Possible spelling mistake found.',
                        'shortMessage' => 'Spelling mistake',
                        'offset' => 6,
                        'length' => 5,
                        'text' => 'color',
                        'replacements' => [
                            ['value' => 'colour']
                        ]
                    ]
                ]
            ], 200)
        ]);

        $text = '<p>Their color is beautiful.</p>';
        $expected = '<p>Their colour is beautiful.</p>';
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_msoffice_attributes_are_stripped()
    {
        $text = '<p class="MsoNormal" style="margin-bottom: 0in; line-height: normal; background: white;"><span lang="TA" style="font-size: 9.0pt; font-family: \'Latha\',sans-serif; color: #080809;">அதன் ஆசை</span></p>';
        // Style should be removed completely since background/color are stripped. Class and lang stripped.
        $expected = '<p><span>அதன் ஆசை</span></p>';
        $this->assertEquals($expected, $this->processor->process($text));
    }

    public function test_image_styles_are_preserved()
    {
        $text = '<p><img src="/storage/images/123.jpg" style="float: left; margin-right: 24px; margin-bottom: 16px; margin-top: 8px; max-width: 45%; font-family: sans-serif;" class="alignleft rounded-xl"></p>';
        // The image styles float, margin, and max-width should be preserved, but font-family should be stripped.
        $expected = '<p><img src="/storage/images/123.jpg" style="float: left; margin-right: 24px; margin-bottom: 16px; margin-top: 8px; max-width: 45%;" class="alignleft rounded-xl"></p>';
        $this->assertEquals($expected, $this->processor->process($text));
    }
}
