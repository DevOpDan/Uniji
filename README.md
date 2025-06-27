# Uniji
A Laravel Package to handle Unicode and HTML entity Emoji's.

## Installation
```bash
composer require devopdan/uniji
```

## Caching the Library
You can pre-cache the library by running the following command:
```bash
php artisan uniji:cache
```

**Note:** *There are little over 4000 emojis in the library. Cache them...*

If needed, you can clear the cache with the following command:
```bash
php artisan uniji:clear
```

## Usage

Import the Uniji facade
```php
use DevOpDan\Uniji\Facades\Uniji;
```

Depending on your needs, you can use the following methods:
```php
// For a single emoji
Uniji::render(':heart:', 'unicode'); // Output: ❤️
Uniji::render(':heart:', 'html'); // Output: ❤️

// Defaults to html format, if no format is specified
Uniji::render(':heart:'); // Output: ❤️
```

If you're working with a paragraph of text, you can use the following method:
```php
// Our paragraph with :shortcodes: in it.
$paragraph = "This package was made with :heart: by DevOpDan";

$paragraph = Uniji::convertShortcodesInParagraphsToUnicode($paragraph) 
// Output: This package was made with ❤️ by DevOpDan

$paragraph = Uniji::convertShortcodesInParagraphsToHtml($paragraph) 
// Output: This package was made with ❤️ by DevOpDan
```
For convenience, two Macros have been provided to the Stringable class to make it easier to convert shortcodes to their unicode or html versions.
```php
$bio = Str::of($user->bio)->shortcodesToUnicode();
$bio = Str::of($user->bio)->shortcodesToHtml();
```

**Gotcha!** - Keep in mind the Shortcodes are case-sensitive, so it is advisable to run 
``shortcodesToUnicode`` and ``shortcodesToHtml`` before chaining the macros.

This would fail for instance
```php
// Assuming that $article->headline = "I :heart: Laravel"

$result = Str::of($article->headline)->title()->shortcodesToHtml();
// Output: I :Heart: Laravel

$result = Str::of($article->headline)->shortcodesToHtml()->title();
// Output: I ❤️ Laravel
```

## Blade

If you're using Blade, you can use the following syntax:
```html
<p>I @uniji(':heart:') Laravel</p> // Output: I ❤️ Laravel
```

You may also use ```@unijiunicode()``` should you require Unicode output via blade.

## Closing

Internally, when converting the shortcodes to their Html or Unicode counterpart, you will
see the same output. It's just a matter of the format you're using.

For instance, if you check the source of the output in your browser, you will actually
see: <code>&#X2764</code>; (Html) or <code>❤</code> (Unicode).
