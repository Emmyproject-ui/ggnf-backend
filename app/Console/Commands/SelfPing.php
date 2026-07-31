<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SelfPing extends Command
{
    protected $signature = 'app:self-ping';
    protected $description = 'Ping the application health endpoint to prevent Render from sleeping';

    public function handle(): int
    {
        $url = rtrim(config('app.url'), '/') . '/up';

        try {
            $response = Http::timeout(10)->get($url);
            $this->info("[Self-Ping] {$url} → HTTP {$response->status()}");
        } catch (\Throwable $e) {
            $this->warn("[Self-Ping] {$url} → Failed: {$e->getMessage()}");
        }

        return self::SUCCESS;
    }
}
