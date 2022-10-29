<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class prepareTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'prepare testing environment to make testing possible';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /// create folder with permisson
        exec('if test -d "/var/www/html/testing"; then echo "folder exists"; else mkdir /var/www/html/testing; fi');
        exec('chmod 777 /var/www/html/testing');

        // create database
        exec('echo "" > /var/www/html/database/test.sqlite');
        \Artisan::call('migrate --env=testing');
        return 0;
    }
}
