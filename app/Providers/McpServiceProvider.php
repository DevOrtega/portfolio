<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpMcp\Laravel\Facades\Mcp;
use App\Infrastructure\Services\OsrmService;

class McpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // ==========================================
        // 1. TOOL: OSRM SERVICE (Ejecución Real)
        // ==========================================
        Mcp::tool('osrm_get_route', function (string $start, string $end, ?array $waypoints = null) {
            // 1. Convertimos el input "lat,lon" a array [lat, lon]
            $startCoord = array_map('floatval', explode(',', $start));
            $endCoord   = array_map('floatval', explode(',', $end));

            $coordinates = [$startCoord];

            // 2. Procesar waypoints intermedios si existen
            if ($waypoints) {
                foreach ($waypoints as $wp) {
                    $coordinates[] = array_map('floatval', explode(',', $wp));
                }
            }

            $coordinates[] = $endCoord;

            // 3. Validamos formato básico
            foreach ($coordinates as $coord) {
                if (count($coord) !== 2) {
                    return ['error' => 'Formato inválido en coordenadas. Usa "lat,lon" (ej: 28.14,-15.43)'];
                }
            }

            // 4. Instanciamos tu servicio (Laravel lo resuelve del contenedor)
            /** @var OsrmService $service */
            $service = app(OsrmService::class);

            // 5. Llamamos a tu método existente
            return $service->getRoute($coordinates);
        })
            ->description('Obtiene la ruta real de conducción entre varios puntos usando el servicio interno de OSRM.')
            ->inputSchema([
                'type' => 'object',
                'properties' => [
                    'start' => ['type' => 'string', 'description' => 'Coordenadas inicio "lat,lon"'],
                    'end'   => ['type' => 'string', 'description' => 'Coordenadas fin "lat,lon"'],
                    'waypoints' => [
                        'type' => 'array', 
                        'description' => 'Lista opcional de coordenadas intermedias "lat,lon"',
                        'items' => ['type' => 'string']
                    ],
                ],
                'required' => ['start', 'end'],
            ]);

        // ==========================================
        // 1.1 TOOL: HIKING ROUTE SERVICE (Senderismo con Elevación)
        // ==========================================
        Mcp::tool('hiking_get_route', function (string $start, string $end, ?array $waypoints = null) {
            $startCoord = array_map('floatval', explode(',', $start));
            $endCoord   = array_map('floatval', explode(',', $end));
            
            $intermediate = [];
            if ($waypoints) {
                foreach ($waypoints as $wp) {
                    $intermediate[] = array_map('floatval', explode(',', $wp));
                }
            }

            if (count($startCoord) !== 2 || count($endCoord) !== 2) {
                return ['error' => 'Formato inválido. Usa "lat,lon"'];
            }

            /** @var \App\Application\Hiking\GetHikingRouteService $service */
            $service = app(\App\Application\Hiking\GetHikingRouteService::class);

            return $service->execute($startCoord, $endCoord, empty($intermediate) ? null : $intermediate);
        })
            ->description('Calcula una ruta de senderismo detallada con perfil de elevación y dificultad.')
            ->inputSchema([
                'type' => 'object',
                'properties' => [
                    'start' => ['type' => 'string', 'description' => 'Coordenadas inicio "lat,lon"'],
                    'end'   => ['type' => 'string', 'description' => 'Coordenadas fin "lat,lon"'],
                    'waypoints' => [
                        'type' => 'array', 
                        'description' => 'Lista opcional de coordenadas intermedias "lat,lon"',
                        'items' => ['type' => 'string']
                    ],
                ],
                'required' => ['start', 'end'],
            ]);

        // ==========================================
        // 2. PROMPT: EXPERTO EN TESTING (PEST)
        // ==========================================
        Mcp::prompt('experto_pest', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un QA Engineer Senior experto en PestPHP.
                Tu objetivo es analizar código y proponer tests robustos.
                Reglas:
                1. Usa siempre la API de Expectativas (`expect($val)->toBe...`).
                2. Prefiere la sintaxis `it('does something', function()...)` sobre `test()`.
                3. Implementa Arch Testing para validar capas de arquitectura.
                4. Usa Datasets para reducir la duplicidad.
                5. Si ves un test de PHPUnit, reescríbelo a Pest.
                EOT
            ];
        })->description('Carga el contexto de un experto en Testing con PestPHP.');

        // ==========================================
        // 3. PROMPT: ARQUITECTURA HEXAGONAL
        // ==========================================
        Mcp::prompt('experto_hexagonal', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como Arquitecto de Software experto en Arquitectura Hexagonal (Ports & Adapters) en Laravel.
                Tus revisiones deben asegurar:
                1. DOMINIO: Debe ser puro PHP. Sin dependencias de `Illuminate\` o Eloquent.
                2. APLICACIÓN: Contiene Casos de Uso y DTOs. Orquesta, no decide.
                3. INFRAESTRUCTURA: Implementaciones concretas (Repositorios Eloquent, OSRMService, Controllers).
                4. La dependencia siempre apunta hacia adentro (Infra -> App -> Dominio).
                5. Critica duramente si ves lógica de negocio en un Controlador.
                EOT
            ];
        })->description('Carga el contexto de un Arquitecto Hexagonal estricto.');

        // ==========================================
        // 4. PROMPT: PRINCIPIOS SOLID
        // ==========================================
        Mcp::prompt('experto_solid', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como Code Reviewer enfocado exclusivamente en principios SOLID.
                Analiza cada clase bajo esta lupa:
                - SRP: ¿La clase tiene una única responsabilidad?
                - OCP: ¿Está abierta a extensión pero cerrada a modificación? (Uso de interfaces/polimorfismo).
                - LSP: ¿Las subclases respetan el contrato del padre?
                - ISP: ¿Interfaces pequeñas y específicas en lugar de generales?
                - DIP: ¿Dependemos de abstracciones y no de concreciones? (Inyección de dependencias).
                EOT
            ];
        })->description('Carga el contexto de un auditor de principios SOLID.');

        // ==========================================
        // 5. PROMPT: LARAVEL BEST PRACTICES
        // ==========================================
        Mcp::prompt('experto_laravel', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un Desarrollador Core de Laravel.
                Tus estándares son:
                1. "Fat Models, Skinny Controllers" (o mejor aún, lógica en Services/Actions).
                2. Uso eficiente de Eloquent (evitar N+1, usar Eager Loading).
                3. Uso de FormRequests para validación.
                4. Uso de Inyección de Dependencias en lugar de Facades dentro de lógica de negocio compleja.
                5. Tipado estricto en PHP 8.2+.
                6. Uso de Queues para tareas pesadas.
                EOT
            ];
        })->description('Carga el contexto de mejores prácticas modernas de Laravel.');

        // ==========================================
        // 6. PROMPT: LARAVEL OPENAPI DOCUMENTATION
        // ==========================================
        Mcp::prompt('experto_doc', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un Desarrollador Core de Laravel especializado en documentación OpenAPI.
                Realiza el análisis de código para generar documentación precisa y revisa la calidad de la misma.
                Usa Swagger/OpenAPI para estructurar la documentación.
                EOT
            ];
        })->description('Carga el contexto de documentación OpenAPI.');

        // ==========================================
        // 7. PROMPT: LARAVEL Y VUE.JS INTERNACIONALIZACIÓN con i18n
        // ==========================================
        Mcp::prompt('experto_lang', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un Desarrollador Core de Laravel especializado en internacionalización con Vue.js y Laravel usando i18n.
                Realiza el análisis de código para asegurar que todas las cadenas de texto estén correctamente internacionalizadas.
                Revisa la calidad de la implementación de i18n en el proyecto.
                EOT
            ];
        })->description('Carga el contexto de internacionalización con i18n.');

        // ==========================================
        // 8. PROMPT: LARAVEL Y VUE.JS DOCUMENTACIÓN MARKDOWN
        // ==========================================
        Mcp::prompt('experto_md', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un Desarrollador Core de Laravel especializado en documentación Markdown con Vue.js y Laravel.
                Realiza el análisis de código para asegurar que toda la documentación esté correctamente estructurada en Markdown.
                Revisa la calidad de la documentación en el proyecto.
                EOT
            ];
        })->description('Carga el contexto de documentación Markdown.');

        // ==========================================
        // 9. PROMPT: LARAVEL DEVOPS DOCKER Y DOCKER-COMPOSE
        // ==========================================
        Mcp::prompt('experto_devops', function () {
            return [
                'role' => 'user',
                'content' => <<<'EOT'
                Actúa como un Desarrollador Core de Laravel especializado en DevOps con Docker y Docker-Compose.
                Realiza el análisis de código para asegurar que toda la configuración de Docker y Docker-Compose esté correctamente estructurada.
                Revisa la calidad de la implementación de DevOps en el proyecto.
                EOT
            ];
        })->description('Carga el contexto de DevOps con Docker y Docker-Compose.');
    }
}
