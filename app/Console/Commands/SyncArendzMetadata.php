<?php

namespace App\Console\Commands;

use App\Jobs\SyncArendzMetadataJob;
use Illuminate\Console\Command;

class SyncArendzMetadata extends Command
{
    protected $signature = 'themeparks:sync-arendz {--queued : Dispatch the sync on the queue instead of running it immediately}';

    protected $description = 'Sync park and attraction metadata from tp.arendz.nl';

    public function handle(): int
    {
        if ($this->option('queued')) {
            SyncArendzMetadataJob::dispatch();
            $this->info('Arendz metadata sync job dispatched.');

            return self::SUCCESS;
        }

        SyncArendzMetadataJob::dispatchSync();
        $this->info('Arendz metadata synced.');

        return self::SUCCESS;
    }
}
