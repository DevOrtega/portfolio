# Project Architecture

*Read this in other languages: English | [Español](ARCHITECTURE.md)*

## Overview

This project implements a **Hexagonal Architecture** (also known as Ports & Adapters) combined with **SOLID** principles, providing a clean, maintainable, and highly testable codebase.

The project has **two main domains**:
- **Portfolio**: Management of projects, experiences, education, and skills
- **Bus**: Real-time bus tracking system (TITSA - Tenerife)

## Architectural Goals

1. **Separation of Concerns**: Each layer has a well-defined purpose
2. **Framework Independence**: Business logic does not depend on Laravel
3. **Testability**: Facilitates unit and integration testing
4. **Flexibility**: Allows changing implementations without affecting other layers
5. **Maintainability**: Organized and easy-to-understand code

## Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│                  Presentation Layer                          │
│              (Controllers, API Resources)                    │
│                                                              │
│  • Handles HTTP requests/responses                          │
│  • Input validation                                         │
│  • JSON response formatting                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│                 Application Layer                            │
│          (Services, Use Cases, DTOs)                         │
│                                                              │
│  • Orchestrates business logic                              │
│  • Coordinates between different entities                   │
│  • Contains no infrastructure logic                         │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│                   Domain Layer                               │
│         (Entities, Value Objects, Rules)                     │
│                                                              │
│  • Contains pure business rules                             │
│  • Immutable domain entities                                │
│  • Framework independent                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│               Infrastructure Layer                           │
│      (Repositories, Database, External APIs)                 │
│                                                              │
│  • Concrete implementations                                 │
│  • Database access (Eloquent)                               │
│  • Integration with external services                       │
└─────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
app/
├── Domain/                          # DOMAIN LAYER
│   ├── Portfolio/
│   │   ├── Entities/                
│   │   │   ├── Project.php          # Project entity (readonly)
│   │   │   ├── Experience.php       # Work experience entity
│   │   │   ├── Education.php        # Education entity
│   │   │   ├── Skill.php            # Skill entity
│   │   │   └── PersonalInfo.php     # Personal info entity
│   │   └── Repositories/            
│   │       ├── ProjectRepositoryInterface.php
│   │       ├── ExperienceRepositoryInterface.php
│   │       ├── EducationRepositoryInterface.php
│   │       ├── SkillRepositoryInterface.php
│   │       └── PersonalInfoRepositoryInterface.php
│   │
│   └── Bus/                         # BUS DOMAIN
│       ├── Entities/
│       │   ├── BusCompany.php       # Bus company entity
│       │   ├── BusStop.php          # Stop entity
│       │   ├── BusLine.php          # Line entity
│       │   └── BusRouteStop.php     # Route stop entity
│       └── Repositories/
│           ├── BusCompanyRepositoryInterface.php
│           ├── BusStopRepositoryInterface.php
│           ├── BusLineRepositoryInterface.php
│           └── BusRouteStopRepositoryInterface.php
│
├── Application/                     # APPLICATION LAYER
│   ├── Portfolio/
│   │   └── Services/                
│   │       ├── ProjectService.php   # Project service
│   │       ├── ExperienceService.php # Experience service
│   │       ├── EducationService.php  # Education service
│   │       ├── SkillService.php      # Skill service
│   │       └── PersonalInfoService.php # Personal info service
│   │
│   └── Bus/                         # BUS SERVICES
│       └── Services/
│           └── BusDataService.php   # Bus data service
│
├── Infrastructure/                  # INFRASTRUCTURE LAYER
│   └── Persistence/
│       └── Eloquent/
│           ├── EloquentExperienceRepository.php
│           ├── EloquentEducationRepository.php
│           ├── EloquentSkillRepository.php
│           ├── EloquentPersonalInfoRepository.php
│           ├── Models/              
│           │   └── ProjectModel.php # Eloquent model (final)
│           └── Repositories/        
│               ├── EloquentProjectRepository.php
│               ├── EloquentBusCompanyRepository.php
│               ├── EloquentBusStopRepository.php
│               └── EloquentBusLineRepository.php
│
└── Http/                            # PRESENTATION LAYER
    └── Controllers/
        ├── Api/                     
        │   ├── ProjectController.php
        │   ├── ExperienceController.php
        │   ├── EducationController.php
        │   ├── SkillController.php
        │   └── PersonalInfoController.php
        │
        └── Bus/                     # BUS CONTROLLER
            └── BusController.php    # Bus data API

resources/js/                        # FRONTEND (Vue.js)
├── components/                      # Reusable components
│   ├── StatsCard.vue
│   ├── LoadingSpinner.vue
│   ├── InfoBanner.vue
│   ├── ProjectCard.vue
│   ├── TimelineItem.vue
│   ├── SectionHeader.vue
│   └── guaguas/                     # BUS COMPONENTS
│       ├── BusMap.vue
│       ├── BusPopup.vue
│       ├── BusMarker.vue
│       ├── BusLegend.vue
│       ├── BusScheduleModal.vue
│       └── BusStats.vue
├── composables/                     # Reusable logic
│   ├── useBusMap.js                 # Map configuration
│   ├── useBusSchedule.js            # Schedule management
│   └── useBusData.js                # Bus data
├── views/                           # Main views
│   ├── HomeView.vue
│   ├── ProjectsView.vue
│   ├── ResumeView.vue
│   └── demos/
│       └── GuaguasTracker.vue       # Bus tracking demo
└── router/                          # Route configuration
    └── index.js
```

## Bus Domain Architecture

The Bus domain follows the same hexagonal architecture, with **SQLite** persistence for static route and stop data:

### Data Model

```
┌──────────────────┐       ┌──────────────────┐
│   bus_companies  │       │    bus_lines     │
├──────────────────┤       ├──────────────────┤
│ id               │──┐    │ id               │
│ name             │  │    │ company_id    ───┼──┐
│ slug             │  │    │ line_number      │  │
│ api_base_url     │  │    │ name             │  │
│ created_at       │  └────┼─────────────────►│  │
│ updated_at       │       │ color            │  │
└──────────────────┘       │ created_at       │  │
                           │ updated_at       │  │
                           └──────────────────┘  │
                                    │            │
                                    │            │
┌──────────────────┐       ┌────────▼───────────┼┐
│    bus_stops     │       │  bus_route_stops   ││
├──────────────────┤       ├────────────────────┤│
│ id               │──┐    │ id                 ││
│ name             │  │    │ line_id         ───┘│
│ code             │  │    │ stop_id         ────┘
│ latitude         │  └────┼───────────────────►│
│ longitude        │       │ direction (outbound/inbound)
│ created_at       │       │ stop_order         │
│ updated_at       │       │ created_at         │
└──────────────────┘       │ updated_at         │
                           └────────────────────┘
```

### Bus Data Flow

```
Frontend (Vue.js)
     │
     ▼
useBusData.js (Composable)
     │
     ├─► GET /api/bus-data ──► BusController
     │                              │
     │                              ▼
     │                        BusDataService
     │                              │
     │            ┌─────────────────┴─────────────────┐
     │            ▼                                   ▼
     │    BusLineRepository              BusStopRepository
     │    (SQLite)                       (SQLite)
     │            │                                   │
     │            └─────────────────┬─────────────────┘
     │                              ▼
     │                        JSON Response
     │                              │
     └◄─────────────────────────────┘
     │
     ▼
useBusMap.js + Leaflet (Interactive map)
```

## Data Flow

### Portfolio Domain: Request Flow (User → Backend → Database)

```
1. HTTP Request
   ↓
2. Router (routes/api.php)
   ↓
3. Controller (Presentation Layer)
   • Validates request
   • Delegates to Service
   ↓
4. Service (Application Layer)
   • Executes business logic
   • Uses Repository (interface)
   ↓
5. Repository (Infrastructure Layer)
   • Queries database (Eloquent)
   • Maps to domain entities
   ↓
6. Domain Entity
   • Returns pure data
   ↓
7. JSON Response
```

### Practical Example

```php
// 1. Request arrives at Controller
GET /api/projects

// 2. ProjectController::index()
public function index(): JsonResponse
{
    $projects = $this->projectService->getAllProjects();
    return response()->json($projects->map->toArray());
}

// 3. ProjectService::getAllProjects()
public function getAllProjects(): Collection
{
    return $this->repository->findAll(); // Uses the interface
}

// 4. EloquentProjectRepository::findAll()
public function findAll(): Collection
{
    return ProjectModel::all()
        ->map(fn($model) => $this->toDomain($model));
}

// 5. Mapping to domain entity
private function toDomain(ProjectModel $model): Project
{
    return new Project(
        id: $model->id,
        title: $model->title,
        // ... more properties
    );
}
```

## SOLID Principles Applied

### 1. Single Responsibility Principle (SRP)
**"A class should have only one reason to change"**

**Application in the project:**
- `ProjectController`: Only handles HTTP requests/responses
- `ProjectService`: Only contains business logic
- `EloquentProjectRepository`: Only manages persistence

```php
// BAD - Controller with multiple responsibilities
class ProjectController {
    public function index() {
        $data = DB::table('projects')->get(); // Direct DB access
        // ... business logic ...
        return response()->json($data);
    }
}

// GOOD - Separated responsibilities
class ProjectController {
    public function index() {
        $projects = $this->projectService->getAllProjects();
        return response()->json($projects->map->toArray());
    }
}
```

### 2. Open/Closed Principle (OCP)
**"Open for extension, closed for modification"**

**Application in the project:**
- New repository implementations without modifying services
- New project types without changing existing code

```php
// Can add MongoProjectRepository without modifying ProjectService
class MongoProjectRepository implements ProjectRepositoryInterface {
    public function findAll(): Collection { /* ... */ }
}

// Service binding in RepositoryServiceProvider
$this->app->bind(
    ProjectRepositoryInterface::class,
    EloquentProjectRepository::class // Easy to change
);
```

### 3. Liskov Substitution Principle (LSP)
**"Subtypes must be substitutable for their base types"**

**Application in the project:**
- Any implementation of `ProjectRepositoryInterface` can substitute another
- The service works the same with Eloquent, Mongo, or any other implementation

```php
// Both implementations are interchangeable
interface ProjectRepositoryInterface {
    public function findAll(): Collection;
    public function findById(int $id): ?Project;
}

class EloquentProjectRepository implements ProjectRepositoryInterface { }
class MongoProjectRepository implements ProjectRepositoryInterface { }
class InMemoryProjectRepository implements ProjectRepositoryInterface { }
```

### 4. Interface Segregation Principle (ISP)
**"Clients should not depend on interfaces they don't use"**

**Application in the project:**
- Small and specific interfaces
- Only necessary methods

```php
// BAD - "Fat" interface with unused methods
interface ProjectRepositoryInterface {
    public function findAll();
    public function findById(int $id);
    public function save(Project $project);
    public function delete(int $id);
    public function sendEmail(Project $project);      // Does not belong here
    public function generatePDF(Project $project);    // Does not belong here
    public function exportToExcel(Collection $projects); // Does not belong here
}

// GOOD - Specific interface
interface ProjectRepositoryInterface {
    public function findAll(): Collection;
    public function findById(int $id): ?Project;
    public function save(Project $project): Project;
    public function delete(int $id): bool;
}
```

### 5. Dependency Inversion Principle (DIP)
**"Depend on abstractions, not implementations"**

**Application in the project:**
- Services depend on interfaces, not concrete implementations
- Inversion of control through dependency injection

```php
// BAD - Dependency on concrete implementation
class ProjectService {
    private EloquentProjectRepository $repository;
    
    public function __construct() {
        $this->repository = new EloquentProjectRepository(); // Tight coupling
    }
}

// GOOD - Dependency on abstraction
class ProjectService {
    public function __construct(
        private readonly ProjectRepositoryInterface $repository
    ) {}
}
```

## Testing Strategy

### Unit Tests (Application Layer)
Test business logic in isolation using **mocks**:

```php
it('retrieves all projects', function () {
    // Arrange
    $repository = Mockery::mock(ProjectRepositoryInterface::class);
    $expectedProjects = collect([/* ... */]);
    $repository->shouldReceive('findAll')
        ->once()
        ->andReturn($expectedProjects);
    
    $service = new ProjectService($repository);
    
    // Act
    $result = $service->getAllProjects();
    
    // Assert
    expect($result)->toBe($expectedProjects);
});
```

### Integration Tests (Presentation Layer)
Test the complete application flow:

```php
it('returns all projects via API', function () {
    // Arrange
    ProjectModel::factory()->count(5)->create();
    
    // Act
    $response = $this->getJson('/api/projects');
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(5)
        ->assertJsonStructure([
            '*' => ['id', 'title', 'description', 'tags']
        ]);
});
```

### Frontend Tests (Components & Composables)

```javascript
// Component Test
it('renders project card with correct data', () => {
  const wrapper = mount(ProjectCard, {
    props: { project: mockProject }
  });
  
  expect(wrapper.find('h3').text()).toBe(mockProject.title);
  expect(wrapper.find('.description').text()).toBe(mockProject.description);
});

// Composable Test
it('calculates responsive zoom correctly', () => {
  const { getResponsiveZoom } = useBusMap();
  
  Object.defineProperty(window, 'innerWidth', { value: 640 });
  expect(getResponsiveZoom()).toBe(9.5);
});
```

## Frontend Architecture (Vue.js)

### Composition API Pattern

```javascript
// Composable: Reusable logic
export function useBusMap() {
  const zoom = ref(10);
  
  const getResponsiveZoom = () => {
    const width = window?.innerWidth ?? 1920;
    return width < 640 ? 9.5 : 10.5;
  };
  
  return { zoom, getResponsiveZoom };
}

// Component: UI and presentation
<script setup>
import { useBusMap } from '@/composables/useBusMap';

const { zoom, getResponsiveZoom } = useBusMap();
</script>
```

### Bus Domain Composables

```javascript
// useBusData.js - Bus data management
export function useBusData(lineNumber, direction = 'outbound') {
  const stops = ref([]);
  const lines = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  const fetchBusData = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get('/api/bus-data', {
        params: { line: lineNumber.value, direction: direction.value }
      });
      stops.value = response.data.stops;
      lines.value = response.data.lines;
    } catch (e) {
      error.value = e.message;
    } finally {
      isLoading.value = false;
    }
  };

  return { stops, lines, isLoading, error, fetchBusData };
}
```

### Component Structure

```
components/
├── Common/              # Reusable components
│   ├── StatsCard.vue   # Typed props, slots, events
│   ├── LoadingSpinner.vue
│   └── InfoBanner.vue
├── Projects/            # Portfolio domain specific
│   └── ProjectCard.vue
├── guaguas/             # Bus domain specific
│   ├── BusMap.vue
│   ├── BusPopup.vue
│   ├── BusMarker.vue
│   ├── BusLegend.vue
│   ├── BusScheduleModal.vue
│   └── BusStats.vue
└── Layout/              # Page structure
    ├── Header.vue
    └── Footer.vue
```

## Benefits of this Architecture

### Maintainability
- Code organized by logical layers
- Easy to understand and navigate
- Changes localized to one layer

### Testability
- Simple unit tests with mocks
- Complete integration tests
- High code coverage

### Scalability
- Easy to add new features
- New implementations without changes
- Code ready to grow

### Flexibility
- Change ORM (Eloquent → Doctrine)
- Change DB (MySQL → MongoDB, SQLite)
- Change cache (Redis → Memcached)
- **Multi-database**: Portfolio uses MySQL, Bus uses SQLite

### Independence
- Business logic without Laravel
- Testable without framework
- Portable to other projects

### Multi-Domain Support
- Isolated domains (Portfolio, Bus)
- Each domain with its own persistence
- Horizontal scalability per domain

## References

- [Hexagonal Architecture](https://alistair.cockburn.us/hexagonal-architecture/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Clean Architecture - Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [SQLite Documentation](https://www.sqlite.org/docs.html)

---

<div align="center">
  <p><strong>Architecture Documentation</strong></p>
  <p>Last updated: January 2025</p>
  <p>Version: 2.1.0</p>
</div>
