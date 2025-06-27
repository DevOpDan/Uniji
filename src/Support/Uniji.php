<?php

namespace DevOpDan\Uniji\Support;

use Illuminate\Support\Facades\Cache;

final class Uniji
{
    protected array $emojiMap = [];

    protected string $emojiFilePath = __DIR__.'/../emojis/uniji.json';

    public function __construct()
    {
        $this->loadEmojiMap();
    }

    protected function loadEmojiMap(): void
    {
        $this->emojiMap = Cache::rememberForever('uniji_emoji_map', function () {
            if (empty($this->emojiMap)) {
                $json = file_get_contents($this->emojiFilePath);
                $emojis = json_decode($json, true);

                foreach ($emojis as $emoji) {
                    $shortname = $emoji['shortname'];
                    $this->emojiMap[$shortname] = $emoji;
                }
            }

            return $this->emojiMap;
        });
    }

    /**
     * @command uniji:cache
     * @return void
     */
    public function cacheEmojiMap(): void
    {
        $this->emojiMap = [];
        Cache::forget('uniji_emoji_map');
        $this->loadEmojiMap();
    }

    public function render(string $shortcode, string $format = 'html'): string
    {
        if (! str_starts_with($shortcode, ':')) {
            $shortcode = ':'.$shortcode;
        }

        if (! str_ends_with($shortcode, ':')) {
            $shortcode .= ':';
        }

        if (! isset($this->emojiMap[$shortcode])) {
            return $shortcode;
        }

        if ($format === 'unicode') {
            return $this->unicodeToUtf8($this->emojiMap[$shortcode]['unicode']);
        }

        return $this->emojiMap[$shortcode]['html'];
    }

    protected function unicodeToUtf8(string $unicode): string
    {
        $codepoints = explode(' ', $unicode);
        $utf8 = '';

        foreach ($codepoints as $codepoint) {
            $utf8 .= $this->codepointToUtf8($codepoint);
        }

        return $utf8;
    }

    protected function codepointToUtf8(string $codepoint): string
    {
        $decimal = hexdec($codepoint);

        if ($decimal < 128) {
            return chr($decimal);
        }

        if ($decimal < 2048) {
            return chr(($decimal >> 6) + 192).chr(($decimal & 63) + 128);
        }

        if ($decimal < 65536) {
            return chr(($decimal >> 12) + 224).chr((($decimal >> 6) & 63) + 128).chr(($decimal & 63) + 128);
        }

        if ($decimal < 2097152) {
            return chr(($decimal >> 18) + 240).chr((($decimal >> 12) & 63) + 128).chr((($decimal >> 6) & 63) + 128).chr(($decimal & 63) + 128);
        }

        return '';
    }

    public function convertShortcodesInParagraphsToUnicode(string $paragraph): string
    {
        return preg_replace_callback('/:[a-z0-9_\-\+]+:/i', function ($matches) {
            // Convert decimal HTML entities to hexadecimal format first.
            $rendered = $this->render($matches[0]);

            return preg_replace_callback('/&#(\d+);/', function ($matches) {
                return '&#x' . strtoupper(dechex((int)$matches[1])) . ';';
            }, $rendered);
        }, $paragraph);
    }

    public function convertShortcodesInParagraphsToHtml(string $paragraph): string
    {
        return preg_replace_callback('/:[a-z0-9_\-\+]+:/i', function ($matches) {
            return $this->render($matches[0], 'unicode');
        }, $paragraph);
    }
}
