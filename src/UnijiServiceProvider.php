<?php

namespace DevOpDan\Uniji;

use DevOpDan\Uniji\Commands\CacheEmojiMapCommand;
use DevOpDan\Uniji\Commands\ClearEmojiMapCommand;
use DevOpDan\Uniji\Facades\Uniji;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;

class UnijiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('uniji', function ($expression) {
            return "<?php echo \DevOpDan\Uniji\Facades\Uniji::render($expression, 'html'); ?>";
        });

        Blade::directive('unijiunicode', function ($expression) {
            return "<?php echo \DevOpDan\Uniji\Facades\Uniji::render($expression, 'unicode'); ?>";
        });

        Stringable::macro('shortcodesToUnicode', function () {
            return str()->of(Uniji::convertShortcodesInParagraphsToUnicode($this->value));
        });

        Stringable::macro('shortcodesToHtml', function () {
            return str()->of(Uniji::convertShortcodesInParagraphsToHtml($this->value));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                CacheEmojiMapCommand::class,
                ClearEmojiMapCommand::class
            ]);
        }
    }

    public function register(): void
    {
        $this->app->singleton('Uniji', function () {
            return new Support\Uniji();
        });
    }
}
