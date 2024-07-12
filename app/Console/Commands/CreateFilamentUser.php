<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class CreateFilamentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-filament-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Filament Admin User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call("make:filament-user --name=Paschal --email=paschal.okafor@troloppe.com --password=12345678");
        User::find(1)->assignRole('Admin');
    }
}