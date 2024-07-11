<?php

namespace App\Console\Commands;

use App\Facades\PruneExpiredTmpImagesFacade;
use Illuminate\Console\Command;

class PruneExpiredTmpImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-expired-tmp-image {minutes=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $msg = PruneExpiredTmpImagesFacade::deleteExpiredImages($this->argument('minutes'));
            $this->info($msg);
        } catch (\Exception $error) {
            $this->error($error->getMessage());
        }
    }
}
