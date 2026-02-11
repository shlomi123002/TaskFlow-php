<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use Illuminate\Console\Command;

class WorkspaceCleanup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'workspace:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Permanently delete soft-deleted workspaces older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        // רק workspaces שנמחקו (soft deleted) ויותר ישנים מ-30 יום
        $query = Workspace::onlyTrashed()
            ->where('deleted_at', '<', $cutoff);

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('No workspaces to cleanup.');
            return Command::SUCCESS;
        }

        // forceDelete = מחיקה לצמיתות
        $query->forceDelete();

        $this->info("Deleted {$count} workspaces permanently.");

        return Command::SUCCESS;
    }
}
