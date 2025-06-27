<?php

namespace DevOpDan\Uniji\Commands;

use DevOpDan\Uniji\Facades\Uniji;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CacheEmojiMapCommand extends Command
{
    protected $signature = 'uniji:cache';

    protected $description = 'Pre-cache the emoji map for faster emoji rendering';

    public function handle(): int
    {
        $this->info('Caching emoji map...');

        Uniji::cacheEmojiMap();

        $this->info('Emoji map cached successfully!');

        return CommandAlias::SUCCESS;
    }
}
