<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportThemeParksJob;

class ImportThemeParks extends Command
{
    protected $signature = 'themeparks:import';
    protected $description = 'Dispatch Theme Parks import job';

    public function handle(): int
    {
        ImportThemeParksJob::dispatch();

        $this->info('Import job dispatched.');

        return Command::SUCCESS;
    }
}