<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\BusCompanyModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusLineModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusStopModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for truncation
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('bus_route_stops')->truncate();
        DB::table('bus_lines')->truncate();
        DB::table('bus_stops')->truncate();
        DB::table('bus_companies')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $this->seedCompanies();
        $this->seedStops();
        $this->seedLines();
    }

    private function seedCompanies(): void
    {
        $companies = [
            [
                'code' => 'municipales',
                'name' => 'Guaguas Municipales',
                'primary_color' => '#FDB913',
                'secondary_color' => '#D49400',
                'text_color' => '#333333',
            ],
            [
                'code' => 'global',
                'name' => 'Global',
                'primary_color' => '#0066CC',
                'secondary_color' => '#004C99',
                'text_color' => '#FFFFFF',
            ],
            [
                'code' => 'night',
                'name' => 'Líneas Nocturnas',
                'primary_color' => '#9933FF',
                'secondary_color' => '#7722CC',
                'text_color' => '#FFFFFF',
            ],
        ];

        foreach ($companies as $company) {
            BusCompanyModel::create($company);
        }

        $this->command->info('✓ Companies seeded');
    }

    private function seedStops(): void
    {
        $stops = $this->getStopsData();

        foreach ($stops as $code => $data) {
            BusStopModel::create([
                'code' => $code,
                'name' => $this->codeToName($code),
                'zone' => $data['zone'] ?? null,
                'lat_outbound' => $data['outbound'][0],
                'lng_outbound' => $data['outbound'][1],
                'lat_inbound' => $data['inbound'][0],
                'lng_inbound' => $data['inbound'][1],
            ]);
        }

        $this->command->info('✓ Stops seeded: ' . count($stops));
    }

    private function seedLines(): void
    {
        $municipales = BusCompanyModel::where('code', 'municipales')->first();
        $global = BusCompanyModel::where('code', 'global')->first();
        $night = BusCompanyModel::where('code', 'night')->first();

        $routes = $this->getRoutesData();
        $linesCreated = 0;
        $stopsCreated = 0;

        foreach ($routes as $route) {
            $company = match ($route['company']) {
                'municipales' => $municipales,
                'global' => $global,
                'night' => $night,
                default => $municipales,
            };

            $line = BusLineModel::create([
                'company_id' => $company->id,
                'line_number' => $route['line'],
                'type' => $route['type'],
                'origin' => $route['origin'],
                'destination' => $route['destination'],
                'color' => $route['color'] ?? null,
                'is_main_line' => $this->isMainLine($route['line'], $route['company']),
            ]);

            $linesCreated++;

            // Add stops for outbound direction
            foreach ($route['stopsOutbound'] as $order => $stopCode) {
                $stop = BusStopModel::where('code', $stopCode)->first();
                if ($stop) {
                    DB::table('bus_route_stops')->insert([
                        'line_id' => $line->id,
                        'stop_id' => $stop->id,
                        'direction' => 'outbound',
                        'order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $stopsCreated++;
                }
            }

            // Add stops for inbound direction
            foreach ($route['stopsInbound'] as $order => $stopCode) {
                $stop = BusStopModel::where('code', $stopCode)->first();
                if ($stop) {
                    DB::table('bus_route_stops')->insert([
                        'line_id' => $line->id,
                        'stop_id' => $stop->id,
                        'direction' => 'inbound',
                        'order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $stopsCreated++;
                }
            }
        }

        $this->command->info("✓ Lines seeded: {$linesCreated}");
        $this->command->info("✓ Route stops seeded: {$stopsCreated}");
    }

    private function isMainLine(string $line, string $company): bool
    {
        $mainLines = [
            'municipales' => ['1', '12', '17', '25', '26'],
            'global' => ['1', '5', '30', '60', '91'],
            'night' => ['L1', 'L2', 'L3'],
        ];

        return in_array($line, $mainLines[$company] ?? []);
    }

    private function codeToName(string $code): string
    {
        // Convert camelCase to Title Case with spaces
        $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $code);
        $name = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $name);
        return ucfirst($name);
    }

    private function getStopsData(): array
    {
        return [
            // TERMINALES E INTERCAMBIADORES
            'teatro' => ['outbound' => [28.1094, -15.4167], 'inbound' => [28.1094, -15.4167], 'zone' => 'TERMINALES'],
            'puerto' => ['outbound' => [28.1453, -15.4310], 'inbound' => [28.1453, -15.4310], 'zone' => 'TERMINALES'],
            'santaCatalina' => ['outbound' => [28.1417, -15.4325], 'inbound' => [28.1417, -15.4325], 'zone' => 'TERMINALES'],
            'hoyaDeLaPlata' => ['outbound' => [28.0702, -15.4187], 'inbound' => [28.0702, -15.4187], 'zone' => 'TERMINALES'],
            'auditorio' => ['outbound' => [28.1358, -15.4465], 'inbound' => [28.1358, -15.4465], 'zone' => 'TERMINALES'],
            'hospitalNegrin' => ['outbound' => [28.1268, -15.4540], 'inbound' => [28.1268, -15.4540], 'zone' => 'TERMINALES'],
            'campusULPGC' => ['outbound' => [28.0702, -15.4533], 'inbound' => [28.0702, -15.4533], 'zone' => 'TERMINALES'],
            'estacionSanTelmo' => ['outbound' => [28.1042, -15.4178], 'inbound' => [28.1042, -15.4178], 'zone' => 'TERMINALES'],
            
            // VEGUETA / TRIANA
            'guiniguada' => ['outbound' => [28.1019, -15.4142], 'inbound' => [28.1021, -15.4140], 'zone' => 'VEGUETA_TRIANA'],
            'sanTelmo' => ['outbound' => [28.1047, -15.4172], 'inbound' => [28.1049, -15.4170], 'zone' => 'VEGUETA_TRIANA'],
            'triana' => ['outbound' => [28.1069, -15.4188], 'inbound' => [28.1071, -15.4186], 'zone' => 'VEGUETA_TRIANA'],
            'vegueta' => ['outbound' => [28.0997, -15.4153], 'inbound' => [28.0999, -15.4151], 'zone' => 'VEGUETA_TRIANA'],
            'mercadoVegueta' => ['outbound' => [28.0989, -15.4141], 'inbound' => [28.0991, -15.4139], 'zone' => 'VEGUETA_TRIANA'],
            'sanBernardo' => ['outbound' => [28.1007, -15.4163], 'inbound' => [28.1009, -15.4161], 'zone' => 'VEGUETA_TRIANA'],
            'catedral' => ['outbound' => [28.0998, -15.4147], 'inbound' => [28.1000, -15.4145], 'zone' => 'VEGUETA_TRIANA'],
            'peregrina' => ['outbound' => [28.1012, -15.4158], 'inbound' => [28.1014, -15.4156], 'zone' => 'VEGUETA_TRIANA'],
            'plazaHurtado' => ['outbound' => [28.1055, -15.4180], 'inbound' => [28.1057, -15.4178], 'zone' => 'VEGUETA_TRIANA'],
            
            // LEÓN Y CASTILLO
            'usosMultiples' => ['outbound' => [28.1090, -15.4217], 'inbound' => [28.1092, -15.4215], 'zone' => 'LEON_CASTILLO'],
            'leonCastillo7' => ['outbound' => [28.1100, -15.4235], 'inbound' => [28.1102, -15.4233], 'zone' => 'LEON_CASTILLO'],
            'leonCastillo22' => ['outbound' => [28.1115, -15.4256], 'inbound' => [28.1117, -15.4254], 'zone' => 'LEON_CASTILLO'],
            'leonCastillo35' => ['outbound' => [28.1128, -15.4270], 'inbound' => [28.1130, -15.4268], 'zone' => 'LEON_CASTILLO'],
            'leonCastillo50' => ['outbound' => [28.1140, -15.4285], 'inbound' => [28.1142, -15.4283], 'zone' => 'LEON_CASTILLO'],
            'leonCastillo67' => ['outbound' => [28.1155, -15.4302], 'inbound' => [28.1157, -15.4300], 'zone' => 'LEON_CASTILLO'],
            'oficinasMunicipales' => ['outbound' => [28.1178, -15.4323], 'inbound' => [28.1180, -15.4321], 'zone' => 'LEON_CASTILLO'],
            'torreLasPalmas' => ['outbound' => [28.1210, -15.4358], 'inbound' => [28.1212, -15.4356], 'zone' => 'LEON_CASTILLO'],
            'rafaelCabrera' => ['outbound' => [28.1195, -15.4340], 'inbound' => [28.1197, -15.4338], 'zone' => 'LEON_CASTILLO'],
            
            // MESA Y LÓPEZ / TOMÁS MORALES
            'mesaYLopez' => ['outbound' => [28.1318, -15.4420], 'inbound' => [28.1320, -15.4418], 'zone' => 'MESA_LOPEZ'],
            'galicia' => ['outbound' => [28.1365, -15.4380], 'inbound' => [28.1367, -15.4378], 'zone' => 'MESA_LOPEZ'],
            'nicolasEstevañez' => ['outbound' => [28.1285, -15.4395], 'inbound' => [28.1287, -15.4393], 'zone' => 'MESA_LOPEZ'],
            'nestor' => ['outbound' => [28.1300, -15.4405], 'inbound' => [28.1302, -15.4403], 'zone' => 'MESA_LOPEZ'],
            'tomasMorales' => ['outbound' => [28.1125, -15.4280], 'inbound' => [28.1127, -15.4278], 'zone' => 'MESA_LOPEZ'],
            'tomasMorales35' => ['outbound' => [28.1145, -15.4310], 'inbound' => [28.1147, -15.4308], 'zone' => 'MESA_LOPEZ'],
            'tomasMorales60' => ['outbound' => [28.1165, -15.4345], 'inbound' => [28.1167, -15.4343], 'zone' => 'MESA_LOPEZ'],
            'pioXII' => ['outbound' => [28.1220, -15.4385], 'inbound' => [28.1222, -15.4383], 'zone' => 'MESA_LOPEZ'],
            
            // PUERTO / SANTA CATALINA / CANTERAS
            'canteras' => ['outbound' => [28.1408, -15.4360], 'inbound' => [28.1410, -15.4358], 'zone' => 'PUERTO_CANTERAS'],
            'lasArenas' => ['outbound' => [28.1420, -15.4375], 'inbound' => [28.1422, -15.4373], 'zone' => 'PUERTO_CANTERAS'],
            'alcaravaneras' => ['outbound' => [28.1312, -15.4320], 'inbound' => [28.1314, -15.4318], 'zone' => 'PUERTO_CANTERAS'],
            'parqueSantaCatalina' => ['outbound' => [28.1405, -15.4330], 'inbound' => [28.1407, -15.4328], 'zone' => 'PUERTO_CANTERAS'],
            'ciudadJardin' => ['outbound' => [28.1260, -15.4290], 'inbound' => [28.1262, -15.4288], 'zone' => 'PUERTO_CANTERAS'],
            'julioVerne' => ['outbound' => [28.1380, -15.4340], 'inbound' => [28.1382, -15.4338], 'zone' => 'PUERTO_CANTERAS'],
            'lopezSocas' => ['outbound' => [28.1430, -15.4350], 'inbound' => [28.1432, -15.4348], 'zone' => 'PUERTO_CANTERAS'],
            'ferreras' => ['outbound' => [28.1395, -15.4320], 'inbound' => [28.1397, -15.4318], 'zone' => 'PUERTO_CANTERAS'],
            
            // ESTADIO / MERCADO
            'estadioInsular' => ['outbound' => [28.1190, -15.4400], 'inbound' => [28.1192, -15.4398], 'zone' => 'ESTADIO_MERCADO'],
            'mercadoCentral' => ['outbound' => [28.1330, -15.4365], 'inbound' => [28.1332, -15.4363], 'zone' => 'ESTADIO_MERCADO'],
            'seccion3' => ['outbound' => [28.1340, -15.4375], 'inbound' => [28.1342, -15.4373], 'zone' => 'ESTADIO_MERCADO'],
            'marivaGomez' => ['outbound' => [28.1350, -15.4385], 'inbound' => [28.1352, -15.4383], 'zone' => 'ESTADIO_MERCADO'],
            
            // FERIA / ESCALERITAS / SCHAMANN
            'donZoilo' => ['outbound' => [28.1040, -15.4260], 'inbound' => [28.1042, -15.4258], 'zone' => 'FERIA_ESCALERITAS'],
            'altavista' => ['outbound' => [28.1075, -15.4320], 'inbound' => [28.1077, -15.4318], 'zone' => 'FERIA_ESCALERITAS'],
            'lasChumberas' => ['outbound' => [28.1090, -15.4380], 'inbound' => [28.1092, -15.4378], 'zone' => 'FERIA_ESCALERITAS'],
            'laFeria' => ['outbound' => [28.1125, -15.4450], 'inbound' => [28.1127, -15.4448], 'zone' => 'FERIA_ESCALERITAS'],
            'feriaNumero5' => ['outbound' => [28.1130, -15.4460], 'inbound' => [28.1132, -15.4458], 'zone' => 'FERIA_ESCALERITAS'],
            'escaleritas' => ['outbound' => [28.1150, -15.4415], 'inbound' => [28.1152, -15.4413], 'zone' => 'FERIA_ESCALERITAS'],
            'escaleritasMercado' => ['outbound' => [28.1155, -15.4425], 'inbound' => [28.1157, -15.4423], 'zone' => 'FERIA_ESCALERITAS'],
            'schamann' => ['outbound' => [28.1115, -15.4385], 'inbound' => [28.1117, -15.4383], 'zone' => 'FERIA_ESCALERITAS'],
            'schamannPlaza' => ['outbound' => [28.1118, -15.4390], 'inbound' => [28.1120, -15.4388], 'zone' => 'FERIA_ESCALERITAS'],
            'laMinilla' => ['outbound' => [28.1330, -15.4420], 'inbound' => [28.1332, -15.4418], 'zone' => 'FERIA_ESCALERITAS'],
            'elPilar' => ['outbound' => [28.1165, -15.4495], 'inbound' => [28.1167, -15.4493], 'zone' => 'FERIA_ESCALERITAS'],
            
            // SIETE PALMAS / TAMARACEITE
            'sietePalmas' => ['outbound' => [28.1205, -15.4580], 'inbound' => [28.1207, -15.4578], 'zone' => 'SIETE_PALMAS'],
            'sietePalmasCC' => ['outbound' => [28.1210, -15.4590], 'inbound' => [28.1212, -15.4588], 'zone' => 'SIETE_PALMAS'],
            'juanCarlosI' => ['outbound' => [28.1250, -15.4515], 'inbound' => [28.1252, -15.4513], 'zone' => 'SIETE_PALMAS'],
            'feloMonzon' => ['outbound' => [28.1120, -15.4555], 'inbound' => [28.1122, -15.4553], 'zone' => 'SIETE_PALMAS'],
            'tamaraceite' => ['outbound' => [28.1380, -15.4720], 'inbound' => [28.1382, -15.4718], 'zone' => 'SIETE_PALMAS'],
            'tenoya' => ['outbound' => [28.1420, -15.4780], 'inbound' => [28.1422, -15.4778], 'zone' => 'SIETE_PALMAS'],
            'arucas' => ['outbound' => [28.1195, -15.5235], 'inbound' => [28.1195, -15.5235], 'zone' => 'NORTE'],
            'teror' => ['outbound' => [28.0590, -15.5475], 'inbound' => [28.0590, -15.5475], 'zone' => 'CENTRO'],
            
            // CONO SUR
            'sanJose' => ['outbound' => [28.0850, -15.4175], 'inbound' => [28.0852, -15.4173], 'zone' => 'CONO_SUR'],
            'paseoSanJose' => ['outbound' => [28.0860, -15.4170], 'inbound' => [28.0862, -15.4168], 'zone' => 'CONO_SUR'],
            'ciudadJusticia' => ['outbound' => [28.0780, -15.4172], 'inbound' => [28.0782, -15.4170], 'zone' => 'CONO_SUR'],
            'blasCabrera' => ['outbound' => [28.0730, -15.4182], 'inbound' => [28.0732, -15.4180], 'zone' => 'CONO_SUR'],
            'elLasso' => ['outbound' => [28.0805, -15.4188], 'inbound' => [28.0807, -15.4186], 'zone' => 'CONO_SUR'],
            'tresPalmas' => ['outbound' => [28.0615, -15.4235], 'inbound' => [28.0617, -15.4233], 'zone' => 'CONO_SUR'],
            'pedroHidalgo' => ['outbound' => [28.0680, -15.4210], 'inbound' => [28.0682, -15.4208], 'zone' => 'CONO_SUR'],
            'casablanca' => ['outbound' => [28.0745, -15.4195], 'inbound' => [28.0747, -15.4193], 'zone' => 'CONO_SUR'],
            'millerBajo' => ['outbound' => [28.0895, -15.4145], 'inbound' => [28.0897, -15.4143], 'zone' => 'CONO_SUR'],
            'millerAlto' => ['outbound' => [28.0910, -15.4155], 'inbound' => [28.0912, -15.4153], 'zone' => 'CONO_SUR'],
            'dominguezAlfonso' => ['outbound' => [28.0820, -15.4165], 'inbound' => [28.0822, -15.4163], 'zone' => 'CONO_SUR'],
            'jinamar' => ['outbound' => [28.0274, -15.4142], 'inbound' => [28.0276, -15.4140], 'zone' => 'CONO_SUR'],
            
            // GUANARTEME / ISLETA
            'guanarteme' => ['outbound' => [28.1340, -15.4450], 'inbound' => [28.1342, -15.4448], 'zone' => 'GUANARTEME_ISLETA'],
            'guanartemeIglesia' => ['outbound' => [28.1345, -15.4455], 'inbound' => [28.1347, -15.4453], 'zone' => 'GUANARTEME_ISLETA'],
            'elRincon' => ['outbound' => [28.1375, -15.4435], 'inbound' => [28.1377, -15.4433], 'zone' => 'GUANARTEME_ISLETA'],
            'baseNaval' => ['outbound' => [28.1475, -15.4325], 'inbound' => [28.1477, -15.4323], 'zone' => 'GUANARTEME_ISLETA'],
            'isleta' => ['outbound' => [28.1520, -15.4295], 'inbound' => [28.1522, -15.4293], 'zone' => 'GUANARTEME_ISLETA'],
            'confital' => ['outbound' => [28.1485, -15.4445], 'inbound' => [28.1487, -15.4443], 'zone' => 'GUANARTEME_ISLETA'],
            
            // TAFIRA / CAMPUS
            'tafiraAlta' => ['outbound' => [28.0770, -15.4520], 'inbound' => [28.0772, -15.4518], 'zone' => 'TAFIRA'],
            'tafiraBaja' => ['outbound' => [28.0830, -15.4470], 'inbound' => [28.0832, -15.4468], 'zone' => 'TAFIRA'],
            'jardinesJamar' => ['outbound' => [28.0690, -15.4545], 'inbound' => [28.0692, -15.4543], 'zone' => 'TAFIRA'],
            
            // OTRAS ZONAS URBANAS
            'lomoApolinario' => ['outbound' => [28.0982, -15.4280], 'inbound' => [28.0984, -15.4278], 'zone' => 'URBANA'],
            'cruzDePiedra' => ['outbound' => [28.0945, -15.4230], 'inbound' => [28.0947, -15.4228], 'zone' => 'URBANA'],
            'lasRehoyas' => ['outbound' => [28.0935, -15.4310], 'inbound' => [28.0937, -15.4308], 'zone' => 'URBANA'],
            'elBatanMiller' => ['outbound' => [28.0920, -15.4265], 'inbound' => [28.0922, -15.4263], 'zone' => 'URBANA'],
            'juanRamon' => ['outbound' => [28.0905, -15.4215], 'inbound' => [28.0907, -15.4213], 'zone' => 'URBANA'],
            'isletaDeportiva' => ['outbound' => [28.1540, -15.4280], 'inbound' => [28.1542, -15.4278], 'zone' => 'URBANA'],
            'darsena' => ['outbound' => [28.1510, -15.4260], 'inbound' => [28.1512, -15.4258], 'zone' => 'URBANA'],
            'laPaterna' => ['outbound' => [28.1080, -15.4340], 'inbound' => [28.1082, -15.4338], 'zone' => 'URBANA'],
            'rehoyas' => ['outbound' => [28.0940, -15.4305], 'inbound' => [28.0942, -15.4303], 'zone' => 'URBANA'],
            'lomoBlanco' => ['outbound' => [28.1020, -15.4350], 'inbound' => [28.1022, -15.4348], 'zone' => 'URBANA'],
            'sanFrancisco' => ['outbound' => [28.1035, -15.4380], 'inbound' => [28.1037, -15.4378], 'zone' => 'URBANA'],
            'sanCristobal' => ['outbound' => [28.0830, -15.4125], 'inbound' => [28.0832, -15.4123], 'zone' => 'URBANA'],
            'lasColoradas' => ['outbound' => [28.0760, -15.4110], 'inbound' => [28.0762, -15.4108], 'zone' => 'URBANA'],
            'elSecadero' => ['outbound' => [28.0880, -15.4290], 'inbound' => [28.0882, -15.4288], 'zone' => 'URBANA'],
            'elBatan' => ['outbound' => [28.0925, -15.4270], 'inbound' => [28.0927, -15.4268], 'zone' => 'URBANA'],
            'sanJuan' => ['outbound' => [28.0975, -15.4125], 'inbound' => [28.0977, -15.4123], 'zone' => 'URBANA'],
            'hospitalJuanCarlosI' => ['outbound' => [28.1015, -15.4395], 'inbound' => [28.1017, -15.4393], 'zone' => 'URBANA'],
            'lomoDeLaCruz' => ['outbound' => [28.0880, -15.4320], 'inbound' => [28.0882, -15.4318], 'zone' => 'URBANA'],
            'carreteraMata' => ['outbound' => [28.0950, -15.4340], 'inbound' => [28.0952, -15.4338], 'zone' => 'URBANA'],
            'carreteraGeneralNorte' => ['outbound' => [28.1060, -15.4355], 'inbound' => [28.1062, -15.4353], 'zone' => 'URBANA'],
            'laBallena' => ['outbound' => [28.1145, -15.4470], 'inbound' => [28.1147, -15.4468], 'zone' => 'URBANA'],
            'lasTorres' => ['outbound' => [28.1135, -15.4435], 'inbound' => [28.1137, -15.4433], 'zone' => 'URBANA'],
            'sanLorenzo' => ['outbound' => [28.1320, -15.4680], 'inbound' => [28.1322, -15.4678], 'zone' => 'URBANA'],
            'almatriche' => ['outbound' => [28.1285, -15.4650], 'inbound' => [28.1287, -15.4648], 'zone' => 'URBANA'],
            'lomoLosFrailes' => ['outbound' => [28.1350, -15.4700], 'inbound' => [28.1352, -15.4698], 'zone' => 'URBANA'],
            'islaPerdida' => ['outbound' => [28.1410, -15.4750], 'inbound' => [28.1412, -15.4748], 'zone' => 'URBANA'],
            'piletas' => ['outbound' => [28.1395, -15.4735], 'inbound' => [28.1397, -15.4733], 'zone' => 'URBANA'],
            'laSuerte' => ['outbound' => [28.1365, -15.4710], 'inbound' => [28.1367, -15.4708], 'zone' => 'URBANA'],
            'hoyaAndrea' => ['outbound' => [28.1230, -15.4620], 'inbound' => [28.1232, -15.4618], 'zone' => 'URBANA'],
            'zarate' => ['outbound' => [28.0580, -15.4260], 'inbound' => [28.0582, -15.4258], 'zone' => 'URBANA'],
            'ciudadDeportivaGC' => ['outbound' => [28.0740, -15.4155], 'inbound' => [28.0742, -15.4153], 'zone' => 'URBANA'],
            'chile' => ['outbound' => [28.1140, -15.4445], 'inbound' => [28.1142, -15.4443], 'zone' => 'URBANA'],
            'paseoSanAntonio' => ['outbound' => [28.1135, -15.4400], 'inbound' => [28.1137, -15.4398], 'zone' => 'URBANA'],
            'sebadal' => ['outbound' => [28.1480, -15.4380], 'inbound' => [28.1482, -15.4378], 'zone' => 'URBANA'],
            'zonaPortuaria' => ['outbound' => [28.1500, -15.4340], 'inbound' => [28.1502, -15.4338], 'zone' => 'URBANA'],
            'sanAntonio' => ['outbound' => [28.1135, -15.4410], 'inbound' => [28.1137, -15.4408], 'zone' => 'URBANA'],
            'lasColoradasPlaya' => ['outbound' => [28.0745, -15.4095], 'inbound' => [28.0747, -15.4093], 'zone' => 'URBANA'],
            
            // GLOBAL - INTERURBANAS
            'telde' => ['outbound' => [27.9941, -15.4166], 'inbound' => [27.9941, -15.4166], 'zone' => 'TELDE'],
            'teldeIntercambiador' => ['outbound' => [27.9945, -15.4170], 'inbound' => [27.9945, -15.4170], 'zone' => 'TELDE'],
            'aeropuerto' => ['outbound' => [27.9319, -15.3866], 'inbound' => [27.9319, -15.3866], 'zone' => 'SUR'],
            'vecindario' => ['outbound' => [27.8414, -15.4489], 'inbound' => [27.8414, -15.4489], 'zone' => 'SUR'],
            'sanAgustin' => ['outbound' => [27.7750, -15.5480], 'inbound' => [27.7750, -15.5480], 'zone' => 'SUR'],
            'playaIngles' => ['outbound' => [27.7580, -15.5720], 'inbound' => [27.7580, -15.5720], 'zone' => 'SUR'],
            'maspalomas' => ['outbound' => [27.7609, -15.5865], 'inbound' => [27.7609, -15.5865], 'zone' => 'SUR'],
            'aguimes' => ['outbound' => [27.9050, -15.4445], 'inbound' => [27.9050, -15.4445], 'zone' => 'SUR'],
            'ingenio' => ['outbound' => [27.9170, -15.4360], 'inbound' => [27.9170, -15.4360], 'zone' => 'SUR'],
            'cruce_de_arinaga' => ['outbound' => [27.8680, -15.4100], 'inbound' => [27.8680, -15.4100], 'zone' => 'SUR'],
            'arinaga' => ['outbound' => [27.8590, -15.3950], 'inbound' => [27.8590, -15.3950], 'zone' => 'SUR'],
            'carrizal' => ['outbound' => [27.9030, -15.4290], 'inbound' => [27.9030, -15.4290], 'zone' => 'SUR'],
            'melenara' => ['outbound' => [27.9885, -15.3760], 'inbound' => [27.9885, -15.3760], 'zone' => 'TELDE'],
            'salinetas' => ['outbound' => [27.9745, -15.3885], 'inbound' => [27.9745, -15.3885], 'zone' => 'TELDE'],
            
            // GLOBAL - SUR Y OESTE
            'faroMaspalomas' => ['outbound' => [27.7350, -15.5915], 'inbound' => [27.7350, -15.5915], 'zone' => 'SUR'],
            'autopistaSur' => ['outbound' => [27.9200, -15.4280], 'inbound' => [27.9200, -15.4280], 'zone' => 'SUR'],
            'puertoMogan' => ['outbound' => [27.8155, -15.7635], 'inbound' => [27.8155, -15.7635], 'zone' => 'OESTE'],
            'arguineguin' => ['outbound' => [27.7610, -15.6815], 'inbound' => [27.7610, -15.6815], 'zone' => 'SUR'],
            'puertoRico' => ['outbound' => [27.7870, -15.7085], 'inbound' => [27.7870, -15.7085], 'zone' => 'OESTE'],
            'taurito' => ['outbound' => [27.8020, -15.7380], 'inbound' => [27.8020, -15.7380], 'zone' => 'OESTE'],
            'tableroMaspalomas' => ['outbound' => [27.7690, -15.5740], 'inbound' => [27.7690, -15.5740], 'zone' => 'SUR'],
            'sanFernando' => ['outbound' => [27.7650, -15.5645], 'inbound' => [27.7650, -15.5645], 'zone' => 'SUR'],
            'bahiaFeliz' => ['outbound' => [27.7940, -15.5120], 'inbound' => [27.7940, -15.5120], 'zone' => 'SUR'],
            'playaCura' => ['outbound' => [27.7915, -15.7150], 'inbound' => [27.7915, -15.7150], 'zone' => 'OESTE'],
            'palmitosPark' => ['outbound' => [27.8060, -15.5780], 'inbound' => [27.8060, -15.5780], 'zone' => 'SUR'],
            'aldeaSanNicolas' => ['outbound' => [27.9845, -15.7795], 'inbound' => [27.9845, -15.7795], 'zone' => 'OESTE'],
            'doctoral' => ['outbound' => [27.8310, -15.4650], 'inbound' => [27.8310, -15.4650], 'zone' => 'SUR'],
            'playaArinaga' => ['outbound' => [27.8520, -15.3865], 'inbound' => [27.8520, -15.3865], 'zone' => 'SUR'],
            'castilloRomeral' => ['outbound' => [27.8175, -15.4760], 'inbound' => [27.8175, -15.4760], 'zone' => 'SUR'],
            'sardina_sur' => ['outbound' => [27.8715, -15.4375], 'inbound' => [27.8715, -15.4375], 'zone' => 'SUR'],
            
            // GLOBAL - NORTE
            'galdar' => ['outbound' => [28.1465, -15.6495], 'inbound' => [28.1465, -15.6495], 'zone' => 'NORTE'],
            'guia' => ['outbound' => [28.1395, -15.6335], 'inbound' => [28.1395, -15.6335], 'zone' => 'NORTE'],
            'agaete' => ['outbound' => [28.1015, -15.6985], 'inbound' => [28.1015, -15.6985], 'zone' => 'NORTE'],
            'puertoNieves' => ['outbound' => [28.0985, -15.7025], 'inbound' => [28.0985, -15.7025], 'zone' => 'NORTE'],
            'moya' => ['outbound' => [28.1095, -15.5870], 'inbound' => [28.1095, -15.5870], 'zone' => 'NORTE'],
            'firgas' => ['outbound' => [28.1090, -15.5615], 'inbound' => [28.1090, -15.5615], 'zone' => 'NORTE'],
            'bananeros' => ['outbound' => [28.1355, -15.5275], 'inbound' => [28.1355, -15.5275], 'zone' => 'NORTE'],
            'cardones' => ['outbound' => [28.1280, -15.5180], 'inbound' => [28.1280, -15.5180], 'zone' => 'NORTE'],
            
            // GLOBAL - CENTRO
            'santaBrigida' => ['outbound' => [28.0340, -15.5005], 'inbound' => [28.0340, -15.5005], 'zone' => 'CENTRO'],
            'sanMateo' => ['outbound' => [28.0135, -15.5320], 'inbound' => [28.0135, -15.5320], 'zone' => 'CENTRO'],
            'tejeda' => ['outbound' => [27.9940, -15.6140], 'inbound' => [27.9940, -15.6140], 'zone' => 'CENTRO'],
            'artenara' => ['outbound' => [28.0205, -15.6445], 'inbound' => [28.0205, -15.6445], 'zone' => 'CENTRO'],
            'valsequillo' => ['outbound' => [27.9870, -15.4955], 'inbound' => [27.9870, -15.4955], 'zone' => 'CENTRO'],
            'tenteniguada' => ['outbound' => [27.9675, -15.5125], 'inbound' => [27.9675, -15.5125], 'zone' => 'CENTRO'],
            
            // Additional stops for routes
            'valleJinamar' => ['outbound' => [28.0275, -15.4205], 'inbound' => [28.0275, -15.4205], 'zone' => 'TELDE'],
            'eucaliptos2' => ['outbound' => [28.0235, -15.4165], 'inbound' => [28.0235, -15.4165], 'zone' => 'TELDE'],
            'ramblasJinamar' => ['outbound' => [28.0210, -15.4185], 'inbound' => [28.0210, -15.4185], 'zone' => 'TELDE'],
            'laHerradura' => ['outbound' => [27.9870, -15.4305], 'inbound' => [27.9870, -15.4305], 'zone' => 'TELDE'],
            'lasRemudas' => ['outbound' => [28.0015, -15.4080], 'inbound' => [28.0015, -15.4080], 'zone' => 'TELDE'],
            'montaneta' => ['outbound' => [28.0105, -15.4050], 'inbound' => [28.0105, -15.4050], 'zone' => 'TELDE'],
            'vialCosteroTelde' => ['outbound' => [27.9765, -15.3895], 'inbound' => [27.9765, -15.3895], 'zone' => 'TELDE'],
            'playaBurrero' => ['outbound' => [27.9620, -15.3710], 'inbound' => [27.9620, -15.3710], 'zone' => 'TELDE'],
            'cementerioSanLazaro' => ['outbound' => [28.0815, -15.4505], 'inbound' => [28.0815, -15.4505], 'zone' => 'TAFIRA'],
            'fondillo' => ['outbound' => [28.0445, -15.4745], 'inbound' => [28.0445, -15.4745], 'zone' => 'TAFIRA'],
            'ciudadCampoAlto' => ['outbound' => [28.0520, -15.4810], 'inbound' => [28.0520, -15.4810], 'zone' => 'TAFIRA'],
            'lomoBlancoCasas' => ['outbound' => [28.0985, -15.4410], 'inbound' => [28.0985, -15.4410], 'zone' => 'URBANA'],
            'tenoyaAlta' => ['outbound' => [28.1455, -15.4820], 'inbound' => [28.1455, -15.4820], 'zone' => 'NORTE'],
            'sanJoseAlamo' => ['outbound' => [28.1030, -15.5085], 'inbound' => [28.1030, -15.5085], 'zone' => 'NORTE'],
            'lanzarote' => ['outbound' => [28.0810, -15.5245], 'inbound' => [28.0810, -15.5245], 'zone' => 'NORTE'],
            'lasMesas' => ['outbound' => [28.1045, -15.4895], 'inbound' => [28.1045, -15.4895], 'zone' => 'NORTE'],
            'elToscon' => ['outbound' => [28.0725, -15.5165], 'inbound' => [28.0725, -15.5165], 'zone' => 'NORTE'],
            'sanBartolomeTirajana' => ['outbound' => [27.9235, -15.5725], 'inbound' => [27.9235, -15.5725], 'zone' => 'CENTRO'],
        ];
    }

    private function getRoutesData(): array
    {
        return [
            // MUNICIPAL LINES
            [
                'line' => '1',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Teatro',
                'destination' => 'Puerto',
                'color' => '#FDB913',
                'stopsOutbound' => ['teatro', 'sanTelmo', 'usosMultiples', 'leonCastillo7', 'leonCastillo22', 'leonCastillo35', 'leonCastillo50', 'leonCastillo67', 'oficinasMunicipales', 'rafaelCabrera', 'torreLasPalmas', 'mesaYLopez', 'galicia', 'santaCatalina', 'puerto'],
                'stopsInbound' => ['puerto', 'parqueSantaCatalina', 'galicia', 'mesaYLopez', 'rafaelCabrera', 'torreLasPalmas', 'oficinasMunicipales', 'leonCastillo67', 'leonCastillo50', 'leonCastillo35', 'leonCastillo22', 'leonCastillo7', 'usosMultiples', 'triana', 'sanTelmo', 'teatro'],
            ],
            [
                'line' => '2',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Guiniguada',
                'destination' => 'Puerto',
                'color' => '#FDB913',
                'stopsOutbound' => ['guiniguada', 'sanBernardo', 'peregrina', 'triana', 'tomasMorales', 'tomasMorales35', 'pioXII', 'estadioInsular', 'mercadoCentral', 'seccion3', 'galicia', 'santaCatalina', 'puerto'],
                'stopsInbound' => ['puerto', 'parqueSantaCatalina', 'galicia', 'mercadoCentral', 'estadioInsular', 'pioXII', 'tomasMorales60', 'tomasMorales35', 'tomasMorales', 'plazaHurtado', 'sanTelmo', 'catedral', 'guiniguada'],
            ],
            [
                'line' => '11',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Teatro',
                'destination' => 'Hospital Dr. Negrín',
                'color' => '#FDB913',
                'stopsOutbound' => ['teatro', 'sanTelmo', 'donZoilo', 'altavista', 'lasChumberas', 'schamann', 'escaleritas', 'laFeria', 'elPilar', 'sietePalmas', 'hospitalNegrin'],
                'stopsInbound' => ['hospitalNegrin', 'sietePalmasCC', 'elPilar', 'feriaNumero5', 'escaleritasMercado', 'schamannPlaza', 'lasChumberas', 'altavista', 'donZoilo', 'triana', 'teatro'],
            ],
            [
                'line' => '12',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Puerto',
                'destination' => 'Hoya de La Plata',
                'color' => '#FDB913',
                'stopsOutbound' => ['puerto', 'santaCatalina', 'galicia', 'mesaYLopez', 'torreLasPalmas', 'oficinasMunicipales', 'ciudadJardin', 'paseoSanJose', 'dominguezAlfonso', 'ciudadJusticia', 'blasCabrera', 'hoyaDeLaPlata'],
                'stopsInbound' => ['hoyaDeLaPlata', 'blasCabrera', 'ciudadJusticia', 'dominguezAlfonso', 'sanJose', 'ciudadJardin', 'alcaravaneras', 'oficinasMunicipales', 'torreLasPalmas', 'mesaYLopez', 'galicia', 'parqueSantaCatalina', 'puerto'],
            ],
            [
                'line' => '17',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Teatro',
                'destination' => 'Auditorio',
                'color' => '#FDB913',
                'stopsOutbound' => ['teatro', 'sanTelmo', 'triana', 'leonCastillo35', 'mesaYLopez', 'elRincon', 'guanarteme', 'auditorio'],
                'stopsInbound' => ['auditorio', 'guanarteme', 'elRincon', 'mesaYLopez', 'leonCastillo35', 'triana', 'sanTelmo', 'teatro'],
            ],
            [
                'line' => '25',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Auditorio',
                'destination' => 'Campus Universitario',
                'color' => '#FDB913',
                'stopsOutbound' => ['auditorio', 'guanarteme', 'pioXII', 'tomasMorales', 'tafiraBaja', 'campusULPGC'],
                'stopsInbound' => ['campusULPGC', 'tafiraBaja', 'tomasMorales', 'pioXII', 'guanarteme', 'auditorio'],
            ],
            [
                'line' => '26',
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => 'Santa Catalina',
                'destination' => 'Campus Universitario',
                'color' => '#FDB913',
                'stopsOutbound' => ['santaCatalina', 'mesaYLopez', 'juanCarlosI', 'sietePalmas', 'feloMonzon', 'tafiraBaja', 'campusULPGC'],
                'stopsInbound' => ['campusULPGC', 'tafiraBaja', 'feloMonzon', 'sietePalmas', 'juanCarlosI', 'mesaYLopez', 'santaCatalina'],
            ],
            
            // GLOBAL LINES
            [
                'line' => '1',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Las Palmas',
                'destination' => 'Puerto de Mogán',
                'color' => '#0066CC',
                'stopsOutbound' => ['estacionSanTelmo', 'telde', 'aeropuerto', 'vecindario', 'sanAgustin', 'playaIngles', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan'],
                'stopsInbound' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'playaIngles', 'sanAgustin', 'vecindario', 'aeropuerto', 'telde', 'estacionSanTelmo'],
            ],
            [
                'line' => '5',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Las Palmas',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stopsOutbound' => ['estacionSanTelmo', 'telde', 'aeropuerto', 'vecindario', 'sanAgustin', 'playaIngles', 'faroMaspalomas'],
                'stopsInbound' => ['faroMaspalomas', 'playaIngles', 'sanAgustin', 'vecindario', 'aeropuerto', 'telde', 'estacionSanTelmo'],
            ],
            [
                'line' => '30',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Santa Catalina',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stopsOutbound' => ['santaCatalina', 'autopistaSur', 'faroMaspalomas'],
                'stopsInbound' => ['faroMaspalomas', 'autopistaSur', 'santaCatalina'],
            ],
            [
                'line' => '60',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Las Palmas',
                'destination' => 'Aeropuerto',
                'color' => '#0066CC',
                'stopsOutbound' => ['santaCatalina', 'autopistaSur', 'aeropuerto'],
                'stopsInbound' => ['aeropuerto', 'autopistaSur', 'santaCatalina'],
            ],
            [
                'line' => '91',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Santa Catalina',
                'destination' => 'Puerto de Mogán',
                'color' => '#0066CC',
                'stopsOutbound' => ['santaCatalina', 'autopistaSur', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan'],
                'stopsInbound' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'autopistaSur', 'santaCatalina'],
            ],
            [
                'line' => '105',
                'type' => 'interurban',
                'company' => 'global',
                'origin' => 'Las Palmas',
                'destination' => 'Gáldar',
                'color' => '#0066CC',
                'stopsOutbound' => ['estacionSanTelmo', 'tamaraceite', 'arucas', 'guia', 'galdar'],
                'stopsInbound' => ['galdar', 'guia', 'arucas', 'tamaraceite', 'estacionSanTelmo'],
            ],
            
            // NIGHT LINES
            [
                'line' => 'L1',
                'type' => 'night',
                'company' => 'night',
                'origin' => 'Puerto',
                'destination' => 'Hoya de La Plata',
                'color' => '#9933FF',
                'stopsOutbound' => ['puerto', 'santaCatalina', 'mesaYLopez', 'leonCastillo35', 'triana', 'sanJose', 'ciudadJusticia', 'hoyaDeLaPlata'],
                'stopsInbound' => ['hoyaDeLaPlata', 'ciudadJusticia', 'sanJose', 'triana', 'leonCastillo35', 'mesaYLopez', 'santaCatalina', 'puerto'],
            ],
            [
                'line' => 'L2',
                'type' => 'night',
                'company' => 'night',
                'origin' => 'Teatro',
                'destination' => 'Santa Catalina',
                'color' => '#9933FF',
                'stopsOutbound' => ['teatro', 'sanTelmo', 'rehoyas', 'cruzDePiedra', 'schamann', 'escaleritas', 'laMinilla', 'hospitalNegrin', 'guanarteme', 'mesaYLopez', 'santaCatalina'],
                'stopsInbound' => ['santaCatalina', 'mesaYLopez', 'guanarteme', 'hospitalNegrin', 'laMinilla', 'escaleritas', 'schamann', 'cruzDePiedra', 'rehoyas', 'sanTelmo', 'teatro'],
            ],
            [
                'line' => 'L3',
                'type' => 'night',
                'company' => 'night',
                'origin' => 'Teatro',
                'destination' => 'Tamaraceite',
                'color' => '#9933FF',
                'stopsOutbound' => ['teatro', 'sanTelmo', 'donZoilo', 'escaleritas', 'laFeria', 'sietePalmas', 'tamaraceite'],
                'stopsInbound' => ['tamaraceite', 'sietePalmas', 'laFeria', 'escaleritas', 'donZoilo', 'sanTelmo', 'teatro'],
            ],
        ];
    }
}
