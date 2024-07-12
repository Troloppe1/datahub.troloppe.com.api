<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;

class PreCpanelDeploy extends Command
{
    private $DEPLOYPATH = "/home/trolpdpy/api.datahub.troloppe.come_path";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pre-cpanel-deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command is used to deploy application to cpanel";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = <<<MESSAGE
        This command runs the following steps:
        (1) Copies .env.production as .env from local machine to remote directory on the cpanel server
        (2) Creates app/Console/Commands directory in the deployment directory
        (3) Copies CreateFilamentUser Artisan Command from local machine to remote
        (4) Handle GIT operations:
            - git checkout master
            - git merge main
            - git push cpanel
            - git checkout main
        
        Kindly ensure all is setup including the .cpanel.yml file before proceeding.
        MESSAGE;

        $answer = $this->confirm($message);
        $this->warn('NOTICE: Ensure the remote path in this script is correct.');

        if (!$answer)
            return $this->error('Aborted');

        $process = Process::pipe(function (Pipe $pipe) {
            // Copy .env file to remote directory
            $pipe->command("rsync -auvz .env.production cpanel:{$this->DEPLOYPATH}/.env");

            if (!file_exists('.deployed')) {

                // Create app/Console/Commands directory in deploy directory 
                $pipe->command("ssh cpanel mkdir -p {$this->DEPLOYPATH}/app/Console/Commands");

                // Copy CreateFilamentUser Artisan Command
                $pipe->command("rsync -auvz app/Console/Commands/CreateFilamentUser.php cpanel:{$this->DEPLOYPATH}/app/Console/Commands/");
            } else {
                $pipe->command('touch .deployed');
            }

            // Handle GIT operations
            $pipe->command([
                'git checkout master',
                'git merge main',
                'git push cpanel',
                'git checkout main'
            ]);

        });
        if (!$process->successful()) {
            throw new ProcessFailedException($process);
        }

        $this->info($process->output());
    }
}
