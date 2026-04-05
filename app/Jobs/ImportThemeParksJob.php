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
use Illuminate\Support\Str;

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

                $destinationModel = Destination::firstOrNew(['api_id' => $destination['id']]);
                $isNew = !$destinationModel->exists;
                
                $destinationModel->name = $destination['name'];
                $destinationModel->slug = $destination['slug'];
                
                if ($isNew) {
                    $destinationModel->is_active = false;
                }
                
                $destinationModel->save();

                $importedDestinationIds[] = $destination['id'];

                foreach ($destination['parks'] as $entity) {
                    $park = $api->park($entity['id']);

                    // Generate a unique slug
                    $baseSlug = Str::slug($entity['name']);
                    $slug = $baseSlug;
                    $counter = 1;

                    // Check if slug exists and make it unique if necessary
                    while (Park::where('slug', $slug)->where('api_id', '!=', $entity['id'])->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $parkModel = Park::firstOrNew(['api_id' => $entity['id']]);
                    $isParkNew = !$parkModel->exists;
                    
                    $parkModel->name = $entity['name'];
                    $parkModel->slug = $slug;
                    $parkModel->destination_id = $destinationModel->id;
                    $parkModel->latitude = $park['location']['latitude'] ?? null;
                    $parkModel->longitude = $park['location']['longitude'] ?? null;
                    
                    if ($isParkNew) {
                        $parkModel->is_active = false;
                    }
                    
                    $parkModel->save();

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