<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all commands needed to install application';

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
        $this->info('Installing mini aspire loan application !');
        $this->info('Run composer install');

        $path = base_path();
        $command = "composer install --optimize-autoloader" ;
        exec("cd {$path} && {$command}");

        $this->info('Run composer dump-autoload');
        $command = "composer dump-autoload";
        exec("cd {$path} && {$command}");

        $this->info('Please update .env file with required credentials !');
        $this->info('The below are optional.');
        if ($this->confirm('Do you cache your configs?')) {
            Artisan::call('config:cache');
        }
        if ($this->confirm('Do you cache your routes?')) {
            Artisan::call('route:cache');
        }
        if ($this->confirm('Do you cache your views? ')) {
            Artisan::call('view:cache');
        }
        if ($this->confirm('Do you want to flush your app cache every time you deploy?')) {
            Artisan::call('cache:clear');
        }

        $this->info('Migrating your tables.');
        Artisan::call('migrate');

        $this->info('Running seeders');
        Artisan::call('db:seed');

        $this->info('Running seeders');
        Artisan::call('key:generate');

        $this->info('Running application !');
        Artisan::call('serve');


    }
}
