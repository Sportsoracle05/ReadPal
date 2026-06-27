<?php
namespace App\Console\Commands;

use App\Services\Ai\AiProviderLogService;
use Illuminate\Console\Command;

class PruneAiLogs extends Command
{
    protected $signature   = 'ai:prune-logs {--days=90 : Delete logs older than N days}';
    protected $description = 'Prune old AI provider log entries to keep the table lean';

    public function handle(): void
    {
        $days    = (int) $this->option('days');
        $deleted = AiProviderLogService::prune($days);

        $this->info("Pruned {$deleted} AI log entries older than {$days} days.");
    }
}