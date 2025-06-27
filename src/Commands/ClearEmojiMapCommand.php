<?php

namespace DevOpDan\Uniji\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ClearEmojiMapCommand extends Command
{
    protected $signature = 'uniji:clear';

    protected $description = 'Clears the emoji map cache without affecting other caches';

    public function handle(): int
    {
        Cache::forget('uniji_emoji_map');
        $this->info('Emoji map cache cleared successfully!');

        return CommandAlias::SUCCESS;
    }
}
