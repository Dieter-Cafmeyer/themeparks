<?php

namespace App\Jobs;

use App\Models\Destination;
use App\Models\Park;
use App\Services\ThemeParksApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportThemeParksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 3;

    public function handle(ThemeParksApi $api): void
    {
        DB::transaction(function () use ($api) {

            $apiDestinations = $api->destinations();
            $apiDestinations = $apiDestinations['destinations'];

            $importedDestinationIds = [];
            $importedParkIds = [];

            foreach ($apiDestinations as $destination) {

                $destinationModel = Destination::updateOrCreate(
                    ['api_id' => $destination['id']],
                    [
                        'name' => $destination['name'],
                        'slug' => $destination['slug'],
                        'is_active' => false,
                    ]
                );

                $importedDestinationIds[] = $destination['id'];

                foreach ($destination['parks'] as $entity) {
                    Park::updateOrCreate(
                        ['api_id' => $entity['id']],
                        [
                            'name' => $entity['name'],
                            'destination_id' => $destinationModel->id,
                            'is_active' => false,
                        ]
                    );

                    $importedParkIds[] = $entity['id'];
                }
            }

            Destination::whereNotIn('api_id', $importedDestinationIds)
                ->update(['is_active' => false]);

            Park::whereNotIn('api_id', $importedParkIds)
                ->update(['is_active' => false]);
        });
    }
}