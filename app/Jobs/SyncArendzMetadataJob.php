<?php

namespace App\Jobs;

use App\Models\AttractionMetadata;
use App\Models\Park;
use App\Services\ArendzThemeParksApi;
use App\Services\ThemeParksApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncArendzMetadataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries = 3;

    public function handle(ArendzThemeParksApi $arendzApi, ThemeParksApi $themeParksApi): void
    {
        $arendzParks = collect($arendzApi->parks());
        $arendzById = $arendzParks->keyBy('id');
        $arendzByName = $arendzParks->keyBy(fn (array $park) => $this->normalize($park['name'] ?? ''));

        Park::query()
            ->where('is_active', true)
            ->get()
            ->each(function (Park $park) use ($arendzApi, $arendzById, $arendzByName, $themeParksApi): void {
                $matchedPark = $this->resolveArendzPark($park, $arendzById, $arendzByName);

                if (!$matchedPark) {
                    return;
                }

                $park->forceFill([
                    'arendz_id' => $matchedPark['id'],
                    'arendz_image_url' => $matchedPark['image'] ?? null,
                    'arendz_description_nl' => $this->plainText($matchedPark['description'] ?? null),
                    'arendz_synced_at' => Carbon::now(),
                ])->save();

                try {
                    $rides = collect($arendzApi->rides($matchedPark['id']));
                    $liveData = collect($themeParksApi->attractions((string) $park->api_id)['liveData'] ?? []);
                } catch (\Throwable) {
                    return;
                }

                $themeParkAttractions = $liveData
                    ->filter(fn (array $item) => str_contains(strtolower($item['entityType'] ?? ''), 'attraction'));

                $attractionsByNormalizedName = $themeParkAttractions
                    ->mapWithKeys(fn (array $attraction) => [
                        $this->normalize($attraction['name'] ?? '') => $attraction,
                    ]);

                DB::transaction(function () use ($park, $rides, $attractionsByNormalizedName): void {
                    foreach ($rides as $ride) {
                        $matchedAttraction = $attractionsByNormalizedName->get($this->normalize($ride['title'] ?? ''));

                        AttractionMetadata::updateOrCreate(
                            [
                                'park_id' => $park->id,
                                'arendz_id' => $ride['id'],
                            ],
                            [
                                'themeparks_api_id' => $matchedAttraction['id'] ?? null,
                                'name' => $ride['title'] ?? null,
                                'arendz_image_url' => $ride['image_url'] ?? ($ride['images'][0] ?? null),
                                'arendz_description_nl' => $this->plainText($ride['description'] ?? null),
                                'arendz_synced_at' => Carbon::now(),
                            ]
                        );
                    }
                });
            });
    }

    protected function resolveArendzPark(Park $park, Collection $arendzById, Collection $arendzByName): ?array
    {
        if ($park->arendz_id && $arendzById->has($park->arendz_id)) {
            return $arendzById->get($park->arendz_id);
        }

        return $arendzByName->get($this->normalize($park->name));
    }

    protected function normalize(?string $value): string
    {
        return Str::slug(Str::lower(trim((string) $value)));
    }

    protected function plainText(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        return html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
