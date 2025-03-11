<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanExpiredCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-expired-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('cache')->where('expiration', '<', now()->timestamp)->delete();
        $this->info("Expired cache records deleted successfully.");
    }
}
