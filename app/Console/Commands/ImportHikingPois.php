<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportHikingPois extends Command
{
    protected $signature = 'hiking:import-pois';
    protected $description = 'Download and import hiking POIs from Overpass API (Gran Canaria)';

    // Gran Canaria Bounding Box
    private const BOUNDS = '27.5,-16.0,28.4,-15.0';
    private const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';

    public function handle(): void
    {
        $this->info("Starting POI import for Gran Canaria...");

        $query = <<<QL
[out:json][timeout:90];
(
  nwr["amenity"~"restaurant|cafe|bar|pub|fast_food|pharmacy|hospital|clinic|doctors|parking|drinking_water"](%s);
  nwr["tourism"~"museum|viewpoint|picnic_site|camp_site|caravan_site|alpine_hut|wilderness_hut|hotel|hostel|guest_house|chalet|apartment|motel"](%s);
  nwr["natural"="peak"](%s);
);
out center;
QL;
        $query = sprintf($query, self::BOUNDS, self::BOUNDS, self::BOUNDS);

        $this->info("Fetching data from Overpass API...");
        
        try {
            $response = Http::timeout(120)->retry(3, 1000)->asForm()->post(self::OVERPASS_URL, [
                'data' => $query
            ]);

            if (!$response->successful()) {
                $this->error("Overpass failed: " . $response->status());
                return;
            }

            $data = $response->json();
            $elements = $data['elements'] ?? [];
            $count = count($elements);
            $this->info("Fetched $count elements. Inserting into database...");

            DB::beginTransaction();
            DB::table('pois')->truncate();

            $chunks = array_chunk($elements, 500);
            $bar = $this->output->createProgressBar(count($elements));

            foreach ($chunks as $chunk) {
                $insertData = [];
                foreach ($chunk as $element) {
                    $tags = $element['tags'] ?? [];
                    $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
                    $lon = $element['lon'] ?? $element['center']['lon'] ?? null;

                    if (!$lat || !$lon) continue;

                    $category = $this->determineCategory($tags);
                    $name = $this->determineName($tags, $category);
                    $relevance = $this->calculateRelevance($tags);

                    $insertData[] = [
                        'osm_id' => $element['id'],
                        'osm_type' => $element['type'],
                        'lat' => $lat,
                        'lon' => $lon,
                        'category' => $category,
                        'name' => $name,
                        'tags' => json_encode($tags),
                        'relevance' => $relevance,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($insertData)) {
                    DB::table('pois')->insert($insertData);
                }
                $bar->advance(count($chunk));
            }

            DB::commit();
            $bar->finish();
            $this->newLine();
            $this->info("Successfully imported POIs.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }

    // Logic copied and adapted from OverpassPoiProvider
    private function determineCategory(array $tags): string
    {
        if (isset($tags['amenity'])) {
            $a = $tags['amenity'];
            if (in_array($a, ['restaurant', 'cafe', 'bar', 'pub', 'fast_food'])) return 'food';
            if ($a === 'pharmacy') return 'health';
            if (in_array($a, ['hospital', 'clinic', 'doctors'])) return 'health';
            if ($a === 'drinking_water') return 'water';
            if ($a === 'parking') return 'parking';
        }
        if (isset($tags['tourism'])) {
            $t = $tags['tourism'];
            if ($t === 'viewpoint') return 'viewpoint';
            if ($t === 'picnic_site') return 'picnic';
            if ($t === 'museum') return 'culture';
            if (in_array($t, ['camp_site', 'caravan_site'])) return 'camping';
            if (in_array($t, ['alpine_hut', 'wilderness_hut'])) return 'shelter';
            if (in_array($t, ['hotel', 'hostel', 'guest_house', 'chalet', 'apartment', 'motel'])) return 'accommodation';
        }
        if (isset($tags['natural']) && $tags['natural'] === 'peak') return 'peak';

        return 'other';
    }

    private function determineName(array $tags, string $category): string
    {
        $name = $tags['name'] 
             ?? $tags['alt_name'] 
             ?? $tags['loc_name'] 
             ?? $tags['short_name']
             ?? $tags['old_name'] 
             ?? $tags['int_name']
             ?? $tags['official_name']
             ?? null;
        
        if (!$name) {
            if ($category === 'peak' && isset($tags['ele'])) {
                $name = "Cima ({$tags['ele']}m)";
            } 
            else if ($category === 'viewpoint') {
                $name = $tags['description'] ?? $tags['note'] ?? "Mirador";
            }
            else {
                $specificTag = $tags['amenity'] ?? $tags['tourism'] ?? $tags['natural'] ?? $tags['shop'] ?? null;
                if ($specificTag && $specificTag !== 'yes') {
                    $name = ucfirst(str_replace('_', ' ', $specificTag));
                } else {
                    $name = ucfirst(str_replace('_', ' ', $category));
                }
            }
        }
        return substr($name, 0, 255);
    }

    private function calculateRelevance(array $tags): int
    {
        $score = 0;
        if (isset($tags['name'])) $score += 10;
        if (isset($tags['wikidata']) || isset($tags['wikipedia'])) $score += 20;
        if (isset($tags['stars'])) $score += (int) $tags['stars'] * 5;
        if (isset($tags['award:michelin'])) $score += 30;
        if (isset($tags['website']) || isset($tags['contact:website'])) $score += 5;
        if (isset($tags['phone']) || isset($tags['contact:phone'])) $score += 5;
        if (isset($tags['opening_hours'])) $score += 2;
        
        return $score;
    }
}
