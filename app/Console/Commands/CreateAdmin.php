<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

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
        $name = $this->ask('Name?', 'Paschal');
        $email = $this->ask('Email?', 'paschal.okafor@troloppe.com');
        $password = $this->secret('Password?');

        while (str($password)->length() < 8) {
            $this->error('Passwords is too short');
            $password = $this->secret('Password?');
        }

        $password_confirmation = $this->secret('Confirm Password?');

        while ($password !== $password_confirmation){
            $this->error('Passwords dont match.');
            $password = $this->secret('Password?');
            $password_confirmation = $this->secret('Confirm Password?');
        }

        try{

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            
            $role = Role::where('name', 'Admin')->first();
            $user->assignRole($role);
            $this->info('Admin created.');
        } catch(Exception $e){
            $this->error('Something went wrong.');
        }
    }
}
