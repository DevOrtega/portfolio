# Guía de Desarrollo - Portfolio

*Leer en otros idiomas: [English](DEVELOPMENT.en.md) | Español*

> **Referencia rápida para desarrolladores**: Guía completa para corregir fallos, añadir funcionalidades y realizar modificaciones de manera eficiente.

---

## Índice de Navegación Rápida

| Tarea | Ir a sección |
|-------|--------------|
| Corregir un bug en el backend | [Debugging Backend](#debugging-backend) |
| Corregir un bug en el frontend | [Debugging Frontend](#debugging-frontend) |
| Añadir un nuevo endpoint API | [Añadir Endpoint](#añadir-nuevo-endpoint-api) |
| Añadir un nuevo componente Vue | [Añadir Componente](#añadir-nuevo-componente-vue) |
| Modificar modelo/entidad | [Modificar Entidad](#modificar-una-entidadmodelo) |
| Añadir una nueva vista/página | [Añadir Vista](#añadir-nueva-vista) |
| Añadir traducciones | [Traducciones](#traducciones-i18n) |
| Ejecutar tests | [Testing](#testing---referencia-rápida) |
| Debuggear tests fallidos | [Tests Fallidos](#debuggear-tests-fallidos) |
| Desplegar cambios | [Despliegue](#despliegue) |
| Trabajar con Bus Domain | [Bus Domain](#bus-domain) |

---

## Estructura del Proyecto

```
portfolio/
├── app/
│   ├── Application/               # LÓGICA DE NEGOCIO
│   │   ├── Portfolio/             # Dominio Portfolio
│   │   │   ├── DTOs/              # Data Transfer Objects
│   │   │   └── Services/          # Servicios (ProjectService, etc.)
│   │   └── Bus/                   # Dominio Bus
│   │       └── Services/          # BusDataService
│   │
│   ├── Domain/                    # DOMINIO PURO
│   │   ├── Portfolio/             # Entidades Portfolio
│   │   │   ├── Entities/          # Project, etc.
│   │   │   └── Repositories/      # Interfaces
│   │   └── Bus/                   # Entidades Bus
│   │       ├── Entities/          # BusCompany, BusStop, BusLine, etc.
│   │       └── Repositories/      # Interfaces de repositorios
│   │
│   ├── Http/Controllers/          # CONTROLADORES
│   │   ├── Api/                   # API Portfolio
│   │   │   ├── ProjectController.php
│   │   │   ├── ExperienceController.php
│   │   │   └── ...
│   │   └── Bus/                   # API Bus
│   │       └── BusController.php
│   │
│   ├── Infrastructure/Persistence/ # PERSISTENCIA
│   │   ├── Eloquent/              # MySQL/SQLite Portfolio
│   │   │   ├── Models/            # Modelos Eloquent
│   │   │   └── Repositories/      # Implementaciones
│   │   └── SQLite/                # SQLite Bus
│   │       ├── Models/            # BusCompanyModel, etc.
│   │       └── Repositories/      # SQLiteBus*Repository
│   │
│   └── Models/                    # Modelos Eloquent (legacy)
│
├── resources/js/                  # FRONTEND VUE.JS
│   ├── components/               # Componentes reutilizables
│   │   └── guaguas/              # Componentes Bus
│   ├── composables/              # Lógica reutilizable
│   │   ├── useBusMap.js          # Mapa
│   │   ├── useBusSchedule.js     # Horarios
│   │   └── useBusData.js         # Datos
│   ├── views/                    # Vistas/Páginas
│   │   └── demos/
│   │       └── GuaguasTracker.vue # Demo Bus
│   ├── locales/                  # Traducciones
│   └── router/                   # Rutas SPA
│
├── routes/
│   ├── api.php                   # Rutas API (incluye /api/bus-data)
│   └── web.php                   # Rutas web
│
├── tests/
│   ├── Feature/Api/              # Tests de integración API
│   └── Unit/Application/         # Tests unitarios
│
└── database/
    ├── migrations/               # Migraciones de BD
    ├── factories/                # Factories para tests
    └── seeders/                  # Datos iniciales (incluye Bus seeders)
```

---

## Comandos Esenciales

### Primera Instalación (tras clonar)

```bash
composer setup
```

Copia .env, instala dependencias PHP/npm, genera key, crea BD SQLite, ejecuta migraciones y seeders, genera docs API, hace build y optimiza.

### Desarrollo Diario

```bash
composer dev
```

Ejecuta en paralelo: servidor Laravel, queue worker, logs en tiempo real (pail), y Vite con HMR.

### Actualizar tras git pull

```bash
composer refresh
```

Reinstala dependencias, resetea BD con seeders, regenera docs API y limpia caches.

### Comandos Frecuentes

```bash
# Tests
php artisan test                      # Todos los tests backend
php artisan test --filter=ProjectApi  # Tests específicos
npm test                              # Tests frontend
npm run test:ui                       # Tests con interfaz visual

# Base de datos
php artisan migrate                   # Ejecutar migraciones pendientes
php artisan migrate:fresh --seed      # Resetear BD con datos

# Documentación API
php artisan l5-swagger:generate       # Regenerar Swagger docs

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Formateo de código
./vendor/bin/pint                     # Formatear PHP
```

---

## Debugging Backend

### Localización Rápida de Archivos

| Si el error está en... | Buscar en... |
|------------------------|--------------|
| Respuesta API incorrecta | `app/Http/Controllers/Api/` |
| Lógica de negocio | `app/Application/Portfolio/Services/` |
| Datos de BD incorrectos | `app/Models/` o `app/Infrastructure/Persistence/Eloquent/` |
| Validación de datos | `app/Http/Requests/` (si existe) o en el Controller |
| Rutas API | `routes/api.php` |

### Flujo de Debugging

```
1. Request llega → routes/api.php
                        ↓
2. Controlador → app/Http/Controllers/Api/{Recurso}Controller.php
                        ↓
3. Service → app/Application/Portfolio/Services/{Recurso}Service.php
                        ↓
4. Repository → app/Infrastructure/Persistence/Eloquent/Repositories/
                        ↓
5. Model/Entidad → app/Models/ o app/Domain/Portfolio/Entities/
```

### Herramientas de Debug

```php
// En cualquier parte del código PHP:
dd($variable);           // Dump and Die
dump($variable);         // Dump sin detener
logger()->info('Msg', ['data' => $data]); // Log a storage/logs/laravel.log

// Ver query SQL ejecutada:
DB::enableQueryLog();
// ... código que hace queries ...
dd(DB::getQueryLog());

// En el terminal:
php artisan tinker       # REPL interactivo
```

### Ver Logs

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Ver últimas 50 líneas y seguir
tail -n 50 -f storage/logs/laravel.log

# O con Docker:
docker compose logs -f --tail 50
```

---

## Debugging Frontend

### Localización Rápida de Archivos

| Si el error está en... | Buscar en... |
|------------------------|--------------|
| Vista/página específica | `resources/js/views/` |
| Componente reutilizable | `resources/js/components/` |
| Lógica compartida | `resources/js/composables/` |
| Rutas SPA | `resources/js/router/index.js` |
| Traducciones | `resources/js/locales/` |
| Estilos globales | `resources/css/` |
| Estado global | `resources/js/stores/` (Pinia) |

### Herramientas de Debug

```javascript
// En componentes Vue:
console.log('Variable:', variable);
console.table(array);    // Para arrays/objetos

// En template (temporal para debug):
<pre>{{ JSON.stringify(data, null, 2) }}</pre>

// Vue DevTools (extensión del navegador)
// - Inspeccionar estado de componentes
// - Ver props y eventos
// - Depurar Pinia store
```

### Errores Comunes y Soluciones

| Error | Causa Probable | Solución |
|-------|----------------|----------|
| `Cannot read property of undefined` | Datos async no cargados | Usar `v-if` o optional chaining `?.` |
| `Component not found` | Import incorrecto | Verificar path y nombre del componente |
| `CORS error` | API en diferente puerto | Verificar config Vite proxy |
| `404 en refresh de SPA` | Rutas no configuradas | Verificar `routes/web.php` fallback |

---

## Añadir Nuevo Endpoint API

### Checklist

```
[ ] 1. Crear/modificar Controller en app/Http/Controllers/Api/
[ ] 2. Añadir ruta en routes/api.php
[ ] 3. Crear Service en app/Application/Portfolio/Services/ (si hay lógica)
[ ] 4. Crear/modificar Repository Interface en app/Domain/Portfolio/Repositories/
[ ] 5. Implementar Repository en app/Infrastructure/Persistence/Eloquent/Repositories/
[ ] 6. Registrar binding en app/Providers/RepositoryServiceProvider.php
[ ] 7. Añadir test en tests/Feature/Api/
[ ] 8. Regenerar docs: php artisan l5-swagger:generate
```

### Ejemplo: Añadir endpoint GET /api/certifications

**1. Crear Controller:**
```php
// app/Http/Controllers/Api/CertificationController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\JsonResponse;

final class CertificationController extends Controller
{
    public function index(): JsonResponse
    {
        $certifications = Certification::orderBy('date', 'desc')->get();
        return response()->json($certifications);
    }
}
```

**2. Añadir ruta:**
```php
// routes/api.php
Route::get('/certifications', [CertificationController::class, 'index']);
```

**3. Crear test:**
```php
// tests/Feature/Api/CertificationApiTest.php
<?php

use App\Models\Certification;

it('returns all certifications', function () {
    Certification::factory()->count(3)->create();
    
    $response = $this->getJson('/api/certifications');
    
    $response->assertStatus(200)
        ->assertJsonCount(3);
});
```

---

## Añadir Nuevo Componente Vue

### Checklist

```
[ ] 1. Crear componente en resources/js/components/
[ ] 2. Usar <script setup> con Composition API
[ ] 3. Definir props con tipos
[ ] 4. Crear test en resources/js/components/__tests__/
[ ] 5. Importar donde se use
```

### Template de Componente

```vue
<!-- resources/js/components/MiComponente.vue -->
<script setup>
import { ref, computed } from 'vue';

// Props con tipos y valores por defecto
const props = defineProps({
  titulo: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  }
});

// Emits tipados
const emit = defineEmits(['seleccionar', 'cerrar']);

// Estado reactivo
const isOpen = ref(false);

// Computed
const itemCount = computed(() => props.items.length);

// Métodos
const handleClick = (item) => {
  emit('seleccionar', item);
};
</script>

<template>
  <div class="mi-componente">
    <h2>{{ titulo }}</h2>
    <span>{{ itemCount }} items</span>
    <ul>
      <li 
        v-for="item in items" 
        :key="item.id"
        @click="handleClick(item)"
      >
        {{ item.name }}
      </li>
    </ul>
  </div>
</template>

<style scoped>
.mi-componente {
  @apply p-4 bg-gray-800 rounded-lg;
}
</style>
```

### Template de Test

```javascript
// resources/js/components/__tests__/MiComponente.test.js
import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import MiComponente from '../MiComponente.vue';

describe('MiComponente', () => {
  const mockItems = [
    { id: 1, name: 'Item 1' },
    { id: 2, name: 'Item 2' }
  ];

  it('renderiza el título correctamente', () => {
    const wrapper = mount(MiComponente, {
      props: { titulo: 'Test Title', items: [] }
    });
    
    expect(wrapper.find('h2').text()).toBe('Test Title');
  });

  it('muestra el conteo correcto de items', () => {
    const wrapper = mount(MiComponente, {
      props: { titulo: 'Test', items: mockItems }
    });
    
    expect(wrapper.text()).toContain('2 items');
  });

  it('emite evento al hacer click en item', async () => {
    const wrapper = mount(MiComponente, {
      props: { titulo: 'Test', items: mockItems }
    });
    
    await wrapper.find('li').trigger('click');
    
    expect(wrapper.emitted('seleccionar')).toBeTruthy();
    expect(wrapper.emitted('seleccionar')[0]).toEqual([mockItems[0]]);
  });
});
```

---

## Modificar una Entidad/Modelo

### Checklist

```
[ ] 1. Crear migración: php artisan make:migration add_campo_to_tabla
[ ] 2. Modificar modelo en app/Models/
[ ] 3. Modificar entidad de dominio en app/Domain/Portfolio/Entities/
[ ] 4. Actualizar Repository si es necesario
[ ] 5. Actualizar Factory en database/factories/
[ ] 6. Actualizar Seeder en database/seeders/
[ ] 7. Actualizar tests
[ ] 8. Ejecutar: php artisan migrate
```

### Ejemplo: Añadir campo "featured" a Projects

**1. Crear migración:**
```bash
php artisan make:migration add_featured_to_projects_table
```

**2. Editar migración:**
```php
// database/migrations/xxxx_add_featured_to_projects_table.php
public function up(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->boolean('featured')->default(false)->after('url');
    });
}

public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropColumn('featured');
    });
}
```

**3. Actualizar modelo:**
```php
// app/Models/Project.php
protected $fillable = [
    // ... campos existentes
    'featured',
];

protected $casts = [
    // ... casts existentes
    'featured' => 'boolean',
];
```

**4. Ejecutar migración:**
```bash
php artisan migrate
```

---

## Añadir Nueva Vista

### Checklist

```
[ ] 1. Crear vista en resources/js/views/
[ ] 2. Añadir ruta en resources/js/router/index.js
[ ] 3. Añadir link en navegación (NavBar.vue)
[ ] 4. Añadir traducciones si es necesario
[ ] 5. Crear tests
```

### Ejemplo: Añadir página de Certificaciones

**1. Crear vista:**
```vue
<!-- resources/js/views/CertificationsView.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SectionHeader from '@/components/SectionHeader.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

const certifications = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/certifications');
    certifications.value = data;
  } catch (error) {
    console.error('Error fetching certifications:', error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <SectionHeader 
      :title="$t('certifications.title')" 
      icon="certificate" 
    />
    
    <LoadingSpinner v-if="loading" />
    
    <div v-else class="grid gap-4">
      <!-- contenido -->
    </div>
  </div>
</template>
```

**2. Añadir ruta:**
```javascript
// resources/js/router/index.js
{
  path: '/certifications',
  name: 'certifications',
  component: () => import('@/views/CertificationsView.vue'),
  meta: { title: 'Certificaciones' }
}
```

---

## Traducciones (i18n)

### Archivos de Traducciones

| Idioma | Frontend | Backend |
|--------|----------|---------|
| Español | `resources/js/locales/es.json` | `lang/es.json` |
| Inglés | `resources/js/locales/en.json` | `lang/en.json` |

### Añadir Nueva Traducción

**Frontend (Vue i18n):**
```json
// resources/js/locales/es.json
{
  "certifications": {
    "title": "Certificaciones",
    "issued_by": "Expedido por",
    "date": "Fecha"
  }
}
```

```json
// resources/js/locales/en.json
{
  "certifications": {
    "title": "Certifications",
    "issued_by": "Issued by",
    "date": "Date"
  }
}
```

**Uso en componentes:**
```vue
<template>
  <h1>{{ $t('certifications.title') }}</h1>
</template>
```

---

## Testing - Referencia Rápida

### Ejecutar Tests

```bash
# Backend
php artisan test                              # Todos
php artisan test --filter=ProjectApiTest      # Por clase
php artisan test --filter="returns all"       # Por nombre de test
php artisan test tests/Feature/Api            # Por directorio

# Frontend
npm test                                      # Todos
npm test -- --grep="ProjectCard"              # Por nombre
npm run test:ui                               # Con UI interactiva
npm run test:coverage                         # Con cobertura
```

### Ubicación de Tests

| Tipo | Backend | Frontend |
|------|---------|----------|
| API/Integración | `tests/Feature/Api/` | - |
| Unitarios Services | `tests/Unit/Application/Services/` | - |
| Componentes | - | `resources/js/components/__tests__/` |
| Composables | - | `resources/js/composables/__tests__/` |

### Debuggear Tests Fallidos

```bash
# Ver más detalle en tests fallidos
php artisan test --stop-on-failure

# Con output verbose
php artisan test -v

# Frontend con más detalle
npm test -- --reporter=verbose
```

### Template Test Backend (Pest)

```php
<?php

use App\Models\Project;

describe('ProjectApiTest', function () {
    
    it('returns all projects', function () {
        Project::factory()->count(3)->create();
        
        $response = $this->getJson('/api/projects');
        
        $response->assertStatus(200)
            ->assertJsonCount(3);
    });

    it('returns 404 when project not found', function () {
        $response = $this->getJson('/api/projects/999');
        
        $response->assertStatus(404);
    });
});
```

---

## Despliegue

### Pre-despliegue Checklist

```
[ ] Todos los tests pasan: php artisan test && npm test
[ ] Código formateado: ./vendor/bin/pint
[ ] No hay errores de consola en el navegador
[ ] Build de producción exitoso: npm run build
[ ] Variables de entorno configuradas
[ ] Migraciones listas
```

### Comandos de Despliegue

```bash
# Build para producción
npm run build

# Optimizaciones Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Con Docker Compose
docker compose up -d --build
docker compose logs -f
```

### Rollback de Docker
docker compose down
docker compose up -d  # Levanta versión anterior si la imagen no cambió
```

---

## Entorno Docker

El proyecto utiliza Docker para un entorno de desarrollo y producción consistente.

### Configuración

- **Base Image**: `serversideup/php:8.3-fpm-nginx` (Optimizada para Laravel)
- **Servidor Web**: Nginx integrado
- **Base de Datos**: SQLite (en archivo montado)
- **Node.js**: Integrado para build de assets

### Variables de Entorno Docker

El archivo `docker-compose.yml` gestiona las variables críticas. Puedes sobreescribir `OSRM_SERVER` si tienes una instancia propia.

```yaml
environment:
  - OSRM_SERVER=${OSRM_SERVER:-https://router.project-osrm.org/route/v1/driving}
```

### Comandos Docker Útiles

```bash
# Reconstruir sin caché (útil tras cambios en Dockerfile)
docker compose build --no-cache

# Entrar al contenedor
docker compose exec portfolio bash

# Ver logs de Nginx/PHP
docker compose logs -f
```

---

## Cheatsheets

### Artisan Commands Frecuentes

```bash
php artisan list                    # Ver todos los comandos
php artisan make:model Nombre -mfc  # Modelo + migración + factory + controller
php artisan make:controller Api/NombreController
php artisan make:migration create_tabla_table
php artisan make:factory NombreFactory
php artisan make:seeder NombreSeeder
php artisan make:test NombreTest --unit
php artisan make:test NombreTest    # Feature test
php artisan route:list             # Ver todas las rutas
php artisan tinker                 # REPL interactivo
```

### Estructura de Respuesta API

```json
// Colección
[
  { "id": 1, "name": "..." },
  { "id": 2, "name": "..." }
]

// Recurso único
{ "id": 1, "name": "...", "details": "..." }

// Error
{
  "message": "Resource not found",
  "errors": {}
}
```

### Patrones Comunes

**Controller (solo HTTP):**
```php
public function index(): JsonResponse
{
    $items = $this->service->getAll();
    return response()->json($items);
}
```

**Service (lógica de negocio):**
```php
public function getAll(): Collection
{
    return $this->repository->findAll();
}
```

**Repository Interface:**
```php
public function findAll(): Collection;
public function findById(int $id): ?Entity;
```

---

## Bus Domain

### Arquitectura

El dominio Bus sigue la misma arquitectura hexagonal que Portfolio, pero usa **SQLite** para datos estáticos:

```
Bus Domain
├── Entities: BusCompany, BusStop, BusLine, BusRouteStop
├── Repositories: Interfaces en app/Domain/Bus/Repositories/
├── Services: BusDataService en app/Application/Bus/Services/
├── Persistence: SQLite en app/Infrastructure/Persistence/SQLite/
└── API: /api/bus-data
```

### Endpoint API

```bash
# Obtener datos de bus
GET /api/bus-data?line=014&direction=outbound

# Parámetros:
# - line: número de línea (ej: "014", "015")
# - direction: "outbound" (ida) o "inbound" (vuelta)
```

### Añadir Nueva Línea de Bus

1. **Crear migración** para datos de paradas
2. **Añadir seeder** con datos de la línea:

```php
// database/seeders/BusLine015Seeder.php
public function run(): void
{
    $company = BusCompanyModel::where('slug', 'titsa')->first();
    
    $line = BusLineModel::create([
        'company_id' => $company->id,
        'line_number' => '015',
        'name' => 'Santa Cruz - La Laguna',
        'color' => '#00AA00',
    ]);
    
    // Añadir paradas...
}
```

3. **Ejecutar seeder**:
```bash
php artisan db:seed --class=BusLine015Seeder
```

### Composables Frontend

```javascript
// Usar datos de bus
import { useBusData } from '@/composables/useBusData';

const lineNumber = ref('014');
const direction = ref('outbound');
const { stops, lines, isLoading, fetchBusData } = useBusData(lineNumber, direction);

// Cargar datos
await fetchBusData();
```

### Testing Bus Domain

```php
// Test del endpoint
it('returns bus data for line 014', function () {
    // Seed data
    $this->seed(BusCompanySeeder::class);
    $this->seed(BusStopsSeeder::class);
    $this->seed(BusLine014Seeder::class);
    
    $response = $this->getJson('/api/bus-data?line=014&direction=outbound');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'stops' => [['id', 'name', 'latitude', 'longitude']],
            'lines' => [['line_number', 'name', 'color']]
        ]);
});
```

---

## Enlaces Útiles

- **Swagger UI Local**: http://localhost:8000/api/documentation
- **Logs Laravel**: `storage/logs/laravel.log`
- **Vue DevTools**: Extensión del navegador
- **Docs Laravel**: https://laravel.com/docs
- **Docs Vue 3**: https://vuejs.org/guide/
- **Docs Pest**: https://pestphp.com/docs
- **Docs Vitest**: https://vitest.dev/guide/
- **Docs SQLite**: https://www.sqlite.org/docs.html

---

**Guía de Desarrollo - Portfolio**

Última actualización: Diciembre 2025  
Versión: 2.2.0
