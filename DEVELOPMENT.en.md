# Development Guide - Portfolio

*Read in other languages: [Español](DEVELOPMENT.md) | English*

> **Quick reference for developers**: Complete guide to fixing bugs, adding features, and making modifications efficiently.

---

## Quick Navigation Index

| Task | Go to section |
|-------|--------------|
| Fix a backend bug | [Debugging Backend](#debugging-backend) |
| Fix a frontend bug | [Debugging Frontend](#debugging-frontend) |
| Add a new API endpoint | [Add Endpoint](#add-new-api-endpoint) |
| Add a new Vue component | [Add Component](#add-new-vue-component) |
| Modify model/entity | [Modify Entity](#modify-an-entitymodel) |
| Add a new view/page | [Add View](#add-new-view) |
| Add translations | [Translations](#translations-i18n) |
| Run tests | [Testing](#testing---quick-reference) |
| Debug failed tests | [Failed Tests](#debug-failed-tests) |
| Deploy changes | [Deployment](#deployment) |
| Work with Bus Domain | [Bus Domain](#bus-domain) |

---

## Project Structure

```
portfolio/
├── app/
│   ├── Application/               # BUSINESS LOGIC
│   │   ├── Portfolio/             # Portfolio Domain
│   │   │   ├── DTOs/              # Data Transfer Objects
│   │   │   └── Services/          # Services (ProjectService, etc.)
│   │   └── Bus/                   # Bus Domain
│   │       └── Services/          # BusDataService
│   │
│   ├── Domain/                    # PURE DOMAIN
│   │   ├── Portfolio/             # Portfolio Entities
│   │   │   ├── Entities/          # Project, etc.
│   │   │   └── Repositories/      # Interfaces
│   │   └── Bus/                   # Bus Entities
│   │       ├── Entities/          # BusCompany, BusStop, BusLine, etc.
│   │       └── Repositories/      # Repository Interfaces
│   │
│   ├── Http/Controllers/          # CONTROLLERS
│   │   ├── Api/                   # Portfolio API
│   │   │   ├── ProjectController.php
│   │   │   ├── ExperienceController.php
│   │   │   └── ...
│   │   └── Bus/                   # Bus API
│   │       └── BusController.php
│   │
│   ├── Infrastructure/Persistence/ # PERSISTENCE
│   │   ├── Eloquent/              # MySQL/SQLite Portfolio
│   │   │   ├── Models/            # Eloquent Models
│   │   │   └── Repositories/      # Implementations
│   │   └── SQLite/                # SQLite Bus
│   │       ├── Models/            # BusCompanyModel, etc.
│   │       └── Repositories/      # SQLiteBus*Repository
│   │
│   └── Models/                    # Eloquent Models (legacy)
│
├── resources/js/                  # VUE.JS FRONTEND
│   ├── components/               # Reusable Components
│   │   └── guaguas/              # Bus Components
│   ├── composables/              # Reusable Logic
│   │   ├── useBusMap.js          # Map
│   │   ├── useBusSchedule.js     # Schedules
│   │   └── useBusData.js         # Data
│   ├── views/                    # Views/Pages
│   │   └── demos/
│   │       └── GuaguasTracker.vue # Bus Demo
│   ├── locales/                  # Translations
│   └── router/                   # SPA Routes
│
├── routes/
│   ├── api.php                   # API Routes (includes /api/bus-data)
│   └── web.php                   # Web Routes
│
├── tests/
│   ├── Feature/Api/              # API Integration Tests
│   └── Unit/Application/         # Unit Tests
│
└── database/
    ├── migrations/               # DB Migrations
    ├── factories/                # Test Factories
    └── seeders/                  # Initial Data (includes Bus seeders)
```

---

## Essential Commands

### First Installation (after cloning)

```bash
composer setup
```

Copies .env, installs PHP/npm dependencies, generates key, creates SQLite DB, runs migrations and seeders, generates API docs, builds and optimizes.

### Daily Development

```bash
composer dev
```

Runs in parallel: Laravel server, queue worker, real-time logs (pail), and Vite with HMR.

### Update after git pull

```bash
composer refresh
```

Reinstalls dependencies, resets DB with seeders, regenerates API docs, and clears caches.

### Frequent Commands

```bash
# Tests
php artisan test                      # All backend tests
php artisan test --filter=ProjectApi  # Specific tests
npm test                              # Frontend tests
npm run test:ui                       # Tests with visual UI

# Database
php artisan migrate                   # Run pending migrations
php artisan migrate:fresh --seed      # Reset DB with data

# API Documentation
php artisan l5-swagger:generate       # Regenerate Swagger docs

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Code Formatting
./vendor/bin/pint                     # Format PHP
```

---

## Debugging Backend

### Quick File Location

| If the error is in... | Look in... |
|------------------------|--------------|
| Incorrect API response | `app/Http/Controllers/Api/` |
| Business logic | `app/Application/Portfolio/Services/` |
| Incorrect DB data | `app/Models/` or `app/Infrastructure/Persistence/Eloquent/` |
| Data validation | `app/Http/Requests/` (if exists) or in the Controller |
| API Routes | `routes/api.php` |

### Debugging Flow

```
1. Request arrives → routes/api.php
                        ↓
2. Controller → app/Http/Controllers/Api/{Resource}Controller.php
                        ↓
3. Service → app/Application/Portfolio/Services/{Resource}Service.php
                        ↓
4. Repository → app/Infrastructure/Persistence/Eloquent/Repositories/
                        ↓
5. Model/Entity → app/Models/ or app/Domain/Portfolio/Entities/
```

### Debug Tools

```php
// Anywhere in PHP code:
dd($variable);           // Dump and Die
dump($variable);         // Dump without stopping
logger()->info('Msg', ['data' => $data]); // Log to storage/logs/laravel.log

// View executed SQL query:
DB::enableQueryLog();
// ... code that runs queries ...
dd(DB::getQueryLog());

// In terminal:
php artisan tinker       # Interactive REPL
```

### View Logs

```bash
# View real-time logs
tail -f storage/logs/laravel.log

# View last 50 lines and follow
tail -n 50 -f storage/logs/laravel.log

# Or with Docker:
docker compose logs -f --tail 50
```

---

## Debugging Frontend

### Quick File Location

| If the error is in... | Look in... |
|------------------------|--------------|
| Specific view/page | `resources/js/views/` |
| Reusable component | `resources/js/components/` |
| Shared logic | `resources/js/composables/` |
| SPA Routes | `resources/js/router/index.js` |
| Translations | `resources/js/locales/` |
| Global styles | `resources/css/` |
| Global state | `resources/js/stores/` (Pinia) |

### Debug Tools

```javascript
// In Vue components:
console.log('Variable:', variable);
console.table(array);    // For arrays/objects

// In template (temporary for debug):
<pre>{{ JSON.stringify(data, null, 2) }}</pre>

// Vue DevTools (browser extension)
// - Inspect component state
// - View props and events
// - Debug Pinia store
```

### Common Errors and Solutions

| Error | Probable Cause | Solution |
|-------|----------------|----------|
| `Cannot read property of undefined` | Async data not loaded | Use `v-if` or optional chaining `?.` |
| `Component not found` | Incorrect import | Verify path and component name |
| `CORS error` | API on different port | Verify Vite proxy config |
| `404 on SPA refresh` | Routes not configured | Verify `routes/web.php` fallback |

---

## Add New API Endpoint

### Checklist

```
[ ] 1. Create/modify Controller in app/Http/Controllers/Api/
[ ] 2. Add route in routes/api.php
[ ] 3. Create Service in app/Application/Portfolio/Services/ (if there is logic)
[ ] 4. Create/modify Repository Interface in app/Domain/Portfolio/Repositories/
[ ] 5. Implement Repository in app/Infrastructure/Persistence/Eloquent/Repositories/
[ ] 6. Register binding in app/Providers/RepositoryServiceProvider.php
[ ] 7. Add test in tests/Feature/Api/
[ ] 8. Regenerate docs: php artisan l5-swagger:generate
```

### Example: Add GET /api/certifications endpoint

**1. Create Controller:**
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

**2. Add route:**
```php
// routes/api.php
Route::get('/certifications', [CertificationController::class, 'index']);
```

**3. Create test:**
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

## Add New Vue Component

### Checklist

```
[ ] 1. Create component in resources/js/components/
[ ] 2. Use <script setup> with Composition API
[ ] 3. Define props with types
[ ] 4. Create test in resources/js/components/__tests__/
[ ] 5. Import where used
```

### Component Template

```vue
<!-- resources/js/components/MyComponent.vue -->
<script setup>
import { ref, computed } from 'vue';

// Props with types and default values
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    default: () => []
  }
});

// Typed emits
const emit = defineEmits(['select', 'close']);

// Reactive state
const isOpen = ref(false);

// Computed
const itemCount = computed(() => props.items.length);

// Methods
const handleClick = (item) => {
  emit('select', item);
};
</script>

<template>
  <div class="my-component">
    <h2>{{ title }}</h2>
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
.my-component {
  @apply p-4 bg-gray-800 rounded-lg;
}
</style>
```

### Test Template

```javascript
// resources/js/components/__tests__/MyComponent.test.js
import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import MyComponent from '../MyComponent.vue';

describe('MyComponent', () => {
  const mockItems = [
    { id: 1, name: 'Item 1' },
    { id: 2, name: 'Item 2' }
  ];

  it('renders title correctly', () => {
    const wrapper = mount(MyComponent, {
      props: { title: 'Test Title', items: [] }
    });
    
    expect(wrapper.find('h2').text()).toBe('Test Title');
  });

  it('shows correct item count', () => {
    const wrapper = mount(MyComponent, {
      props: { title: 'Test', items: mockItems }
    });
    
    expect(wrapper.text()).toContain('2 items');
  });

  it('emits event when clicking item', async () => {
    const wrapper = mount(MyComponent, {
      props: { title: 'Test', items: mockItems }
    });
    
    await wrapper.find('li').trigger('click');
    
    expect(wrapper.emitted('select')).toBeTruthy();
    expect(wrapper.emitted('select')[0]).toEqual([mockItems[0]]);
  });
});
```

---

## Modify an Entity/Model

### Checklist

```
[ ] 1. Create migration: php artisan make:migration add_field_to_table
[ ] 2. Modify model in app/Models/
[ ] 3. Modify domain entity in app/Domain/Portfolio/Entities/
[ ] 4. Update Repository if necessary
[ ] 5. Update Factory in database/factories/
[ ] 6. Update Seeder in database/seeders/
[ ] 7. Update tests
[ ] 8. Run: php artisan migrate
```

### Example: Add "featured" field to Projects

**1. Create migration:**
```bash
php artisan make:migration add_featured_to_projects_table
```

**2. Edit migration:**
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

**3. Update model:**
```php
// app/Models/Project.php
protected $fillable = [
    // ... existing fields
    'featured',
];

protected $casts = [
    // ... existing casts
    'featured' => 'boolean',
];
```

**4. Run migration:**
```bash
php artisan migrate
```

---

## Add New View

### Checklist

```
[ ] 1. Create view in resources/js/views/
[ ] 2. Add route in resources/js/router/index.js
[ ] 3. Add link in navigation (NavBar.vue)
[ ] 4. Add translations if necessary
[ ] 5. Create tests
```

### Example: Add Certifications page

**1. Create view:**
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
      <!-- content -->
    </div>
  </div>
</template>
```

**2. Add route:**
```javascript
// resources/js/router/index.js
{
  path: '/certifications',
  name: 'certifications',
  component: () => import('@/views/CertificationsView.vue'),
  meta: { title: 'Certifications' }
}
```

---

## Translations (i18n)

### Translation Files

| Language | Frontend | Backend |
|--------|----------|---------|
| Spanish | `resources/js/locales/es.json` | `lang/es.json` |
| English | `resources/js/locales/en.json` | `lang/en.json` |

### Add New Translation

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

**Usage in components:**
```vue
<template>
  <h1>{{ $t('certifications.title') }}</h1>
</template>
```

---

## Testing - Quick Reference

### Run Tests

```bash
# Backend
php artisan test                              # All
php artisan test --filter=ProjectApiTest      # By class
php artisan test --filter="returns all"       # By test name
php artisan test tests/Feature/Api            # By directory

# Frontend
npm test                                      # All
npm test -- --grep="ProjectCard"              # By name
npm run test:ui                               # With interactive UI
npm run test:coverage                         # With coverage
```

### Test Locations

| Type | Backend | Frontend |
|------|---------|----------|
| API/Integration | `tests/Feature/Api/` | - |
| Service Unit Tests | `tests/Unit/Application/Services/` | - |
| Components | - | `resources/js/components/__tests__/` |
| Composables | - | `resources/js/composables/__tests__/` |

### Debug Failed Tests

```bash
# See more detail on failed tests
php artisan test --stop-on-failure

# With verbose output
php artisan test -v

# Frontend with more detail
npm test -- --reporter=verbose
```

### Backend Test Template (Pest)

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

## Deployment

### Pre-deployment Checklist

```
[ ] All tests pass: php artisan test && npm test
[ ] Code formatted: ./vendor/bin/pint
[ ] No console errors in browser
[ ] Production build successful: npm run build
[ ] Environment variables configured
[ ] Migrations ready
```

### Deployment Commands

```bash
# Build for production
npm run build

# Laravel Optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# With Docker Compose
docker compose up -d --build
docker compose logs -f
```

### Docker Rollback
docker compose down
docker compose up -d  # Starts previous version if image hasn't changed
```

---

## Docker Environment

The project uses Docker for a consistent development and production environment.

### Configuration

- **Base Image**: `serversideup/php:8.3-fpm-nginx` (Optimized for Laravel)
- **Web Server**: Integrated Nginx
- **Database**: SQLite (in mounted file)
- **Node.js**: Integrated for asset build

### Docker Environment Variables

The `docker-compose.yml` file manages critical variables. You can override `OSRM_SERVER` if you have your own instance.

```yaml
environment:
  - OSRM_SERVER=${OSRM_SERVER:-https://router.project-osrm.org/route/v1/driving}
```

### Useful Docker Commands

```bash
# Rebuild without cache (useful after Dockerfile changes)
docker compose build --no-cache

# Enter container
docker compose exec portfolio bash

# View Nginx/PHP logs
docker compose logs -f
```

---

## Cheatsheets

### Frequent Artisan Commands

```bash
php artisan list                    # View all commands
php artisan make:model Name -mfc    # Model + migration + factory + controller
php artisan make:controller Api/NameController
php artisan make:migration create_table_table
php artisan make:factory NameFactory
php artisan make:seeder NameSeeder
php artisan make:test NameTest --unit
php artisan make:test NameTest      # Feature test
php artisan route:list             # View all routes
php artisan tinker                 # Interactive REPL
```

### API Response Structure

```json
// Collection
[
  { "id": 1, "name": "..." },
  { "id": 2, "name": "..." }
]

// Single Resource
{ "id": 1, "name": "...", "details": "..." }

// Error
{
  "message": "Resource not found",
  "errors": {}
}
```

### Common Patterns

**Controller (HTTP only):**
```php
public function index(): JsonResponse
{
    $items = $this->service->getAll();
    return response()->json($items);
}
```

**Service (business logic):**
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

### Architecture

The Bus domain follows the same hexagonal architecture as Portfolio, but uses **SQLite** for static data:

```
Bus Domain
├── Entities: BusCompany, BusStop, BusLine, BusRouteStop
├── Repositories: Interfaces in app/Domain/Bus/Repositories/
├── Services: BusDataService in app/Application/Bus/Services/
├── Persistence: SQLite in app/Infrastructure/Persistence/SQLite/
└── API: /api/bus-data
```

### API Endpoint

```bash
# Get bus data
GET /api/bus-data?line=014&direction=outbound

# Parameters:
# - line: line number (e.g. "014", "015")
# - direction: "outbound" or "inbound"
```

### Add New Bus Line

1. **Create migration** for stop data
2. **Add seeder** with line data:

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
    
    // Add stops...
}
```

3. **Run seeder**:
```bash
php artisan db:seed --class=BusLine015Seeder
```

### Frontend Composables

```javascript
// Use bus data
import { useBusData } from '@/composables/useBusData';

const lineNumber = ref('014');
const direction = ref('outbound');
const { stops, lines, isLoading, fetchBusData } = useBusData(lineNumber, direction);

// Load data
await fetchBusData();
```

### Testing Bus Domain

```php
// Endpoint test
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

## Useful Links

- **Local Swagger UI**: http://localhost:8000/api/documentation
- **Laravel Logs**: `storage/logs/laravel.log`
- **Vue DevTools**: Browser Extension
- **Laravel Docs**: https://laravel.com/docs
- **Vue 3 Docs**: https://vuejs.org/guide/
- **Pest Docs**: https://pestphp.com/docs
- **Vitest Docs**: https://vitest.dev/guide/
- **SQLite Docs**: https://www.sqlite.org/docs.html

---

**Development Guide - Portfolio**

Last updated: December 2025
Version: 2.2.0