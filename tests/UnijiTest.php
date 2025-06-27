<?php

namespace DevOpDan\Uniji\Tests;

use Tests;
use DevOpDan\Uniji\Facades\Uniji;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

it('renders an emoji from shortcode to HTML by default', function () {
    expect(Uniji::render(':United_Nations:'))->toBe('&#x1F1FA;&#x1F1F3;');
});

it('renders an emoji from shortcode to Unicode when specified', function () {
    $unicode = Uniji::render(':United_Nations:', 'unicode');
    // The Unicode codepoints for the United Nations flag are 1F1FA 1F1F3
    // We expect these to be converted to UTF-8 characters
    expect($unicode)->not->toBe('&#x1F1FA;&#x1F1F3;')
        ->and($unicode)->toBe(json_decode('"\\uD83C\\uDDFA\\uD83C\\uDDF3"'));
});

it('adds colons to shortcode if not present', function () {
    expect(Uniji::render('United_Nations'))->toBe('&#x1F1FA;&#x1F1F3;');
});

it('returns the original shortcode if not found', function () {
    expect(Uniji::render(':NonExistentEmoji:'))->toBe(':NonExistentEmoji:');
});

it('converts shortcodes in paragraphs to HTML', function () {
    $paragraph = 'Hello :United_Nations: and :grinning:';
    $result = Uniji::convertShortcodesInParagraphsToUnicode($paragraph);

    expect($result)->toBe('Hello &#x1F1FA;&#x1F1F3; and &#x1F600;');
});

it('converts shortcodes in paragraphs to Unicode', function () {
    $paragraph = 'Hello :United_Nations: and :grinning:';
    $result = Uniji::convertShortcodesInParagraphsToHtml($paragraph);

    // The Unicode codepoints should be converted to UTF-8 characters
    expect($result)->toBe('Hello ' . json_decode('"\\uD83C\\uDDFA\\uD83C\\uDDF3"') . ' and ' . json_decode('"\\uD83D\\uDE00"'));
});

it('handles paragraphs with no shortcodes', function () {
    $paragraph = 'Hello world with no emojis';

    expect(Uniji::convertShortcodesInParagraphsToUnicode($paragraph))->toBe($paragraph)
        ->and(Uniji::convertShortcodesInParagraphsToHtml($paragraph))->toBe($paragraph);
});

it('handles paragraphs with multiple instances of the same shortcode', function () {
    $paragraph = 'I love :grinning: so much that I use :grinning: everywhere!';
    $htmlResult = Uniji::convertShortcodesInParagraphsToUnicode($paragraph);
    $unicodeResult = Uniji::convertShortcodesInParagraphsToHtml($paragraph);

    expect($htmlResult)->toBe('I love &#x1F600; so much that I use &#x1F600; everywhere!')
        ->and($unicodeResult)->toBe('I love ' . json_decode('"\\uD83D\\uDE00"') . ' so much that I use ' . json_decode('"\\uD83D\\uDE00"') . ' everywhere!');
});

it('converts shortcodes to HTML entities using the Str facade macro', function () {
    $text = 'Hello :United_Nations: and :grinning:';
    $result = Str::of($text)->shortcodesToUnicode();

    expect($result->toString())->toBe('Hello &#x1F1FA;&#x1F1F3; and &#x1F600;');
});

it('converts shortcodes to Unicode characters using the Str facade macro', function () {
    $text = 'Hello :United_Nations: and :grinning:';
    $result = Str::of($text)->shortcodesToHtml();

    // The Unicode codepoints should be converted to UTF-8 characters
    expect($result->toString())->toBe('Hello ' . json_decode('"\\uD83C\\uDDFA\\uD83C\\uDDF3"') . ' and ' . json_decode('"\\uD83D\\uDE00"'));
});

it('handles text with no shortcodes using the Str facade macros', function () {
    $text = 'Hello world with no emojis';

    expect(Str::of($text)->shortcodesToUnicode()->toString())->toBe($text)
        ->and(Str::of($text)->shortcodesToHtml()->toString())->toBe($text);
});
