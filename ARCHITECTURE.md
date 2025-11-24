# Arquitectura del Proyecto

## 📐 Visión General

Este proyecto implementa una **Arquitectura Hexagonal** (también conocida como Ports & Adapters) combinada con los principios **SOLID**, proporcionando una base de código limpia, mantenible y altamente testeable.

## 🎯 Objetivos Arquitectónicos

1. **Separación de Responsabilidades**: Cada capa tiene un propósito bien definido
2. **Independencia de Framework**: La lógica de negocio no depende de Laravel
3. **Testabilidad**: Facilita el testing unitario y de integración
4. **Flexibilidad**: Permite cambiar implementaciones sin afectar otras capas
5. **Mantenibilidad**: Código organizado y fácil de entender

## 🏛️ Capas de la Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                  Presentation Layer                          │
│              (Controllers, API Resources)                    │
│                                                              │
│  • Maneja HTTP requests/responses                           │
│  • Validación de entrada                                    │
│  • Formateo de respuestas JSON                              │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│                 Application Layer                            │
│          (Services, Use Cases, DTOs)                         │
│                                                              │
│  • Orquesta la lógica de negocio                            │
│  • Coordina entre diferentes entidades                       │
│  • No contiene lógica de infraestructura                    │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│                   Domain Layer                               │
│         (Entities, Value Objects, Rules)                     │
│                                                              │
│  • Contiene las reglas de negocio puras                     │
│  • Entidades de dominio inmutables                          │
│  • Independiente de frameworks                              │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports - Interfaces)
┌──────────────────────▼──────────────────────────────────────┐
│               Infrastructure Layer                           │
│      (Repositories, Database, External APIs)                 │
│                                                              │
│  • Implementaciones concretas                               │
│  • Acceso a base de datos (Eloquent)                        │
│  • Integración con servicios externos                       │
└─────────────────────────────────────────────────────────────┘
```

## 📁 Estructura de Directorios

```
app/
├── Domain/                          # 🎯 CAPA DE DOMINIO
│   └── Portfolio/
│       ├── Entities/                
│       │   └── Project.php          # Entidad de dominio (readonly)
│       └── Repositories/            
│           └── ProjectRepositoryInterface.php  # Puerto (interface)
│
├── Application/                     # 🔧 CAPA DE APLICACIÓN
│   └── Portfolio/
│       └── Services/                
│           └── ProjectService.php   # Servicio de aplicación (readonly)
│
├── Infrastructure/                  # 🗄️ CAPA DE INFRAESTRUCTURA
│   └── Persistence/
│       └── Eloquent/
│           ├── Models/              
│           │   └── ProjectModel.php # Modelo Eloquent (final)
│           └── Repositories/        
│               └── EloquentProjectRepository.php  # Adaptador (final)
│
└── Http/                            # 🌐 CAPA DE PRESENTACIÓN
    └── Controllers/
        └── Api/                     
            └── ProjectController.php # Controlador API (final)

resources/js/                        # 🎨 FRONTEND (Vue.js)
├── components/                      # Componentes reutilizables
│   ├── StatsCard.vue
│   ├── LoadingSpinner.vue
│   ├── InfoBanner.vue
│   ├── ProjectCard.vue
│   ├── TimelineItem.vue
│   └── SectionHeader.vue
├── composables/                     # Lógica reutilizable
│   ├── useBusMap.js
│   └── useBusSchedule.js
├── views/                           # Vistas principales
│   ├── HomeView.vue
│   ├── ProjectsView.vue
│   ├── ResumeView.vue
│   └── demos/
│       └── GuaguasTracker.vue
└── router/                          # Configuración de rutas
    └── index.js
```

## 🔄 Flujo de Datos

### Request Flow (Usuario → Backend → Base de Datos)

```
1. HTTP Request
   ↓
2. Router (routes/api.php)
   ↓
3. Controller (Presentation Layer)
   • Valida request
   • Delega al Service
   ↓
4. Service (Application Layer)
   • Ejecuta lógica de negocio
   • Usa Repository (interface)
   ↓
5. Repository (Infrastructure Layer)
   • Consulta base de datos (Eloquent)
   • Mapea a entidades de dominio
   ↓
6. Domain Entity
   • Devuelve datos puros
   ↓
7. Response JSON
```

### Ejemplo Práctico

```php
// 1. Request llega al Controller
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
    return $this->repository->findAll(); // Usa la interface
}

// 4. EloquentProjectRepository::findAll()
public function findAll(): Collection
{
    return ProjectModel::all()
        ->map(fn($model) => $this->toDomain($model));
}

// 5. Mapeo a entidad de dominio
private function toDomain(ProjectModel $model): Project
{
    return new Project(
        id: $model->id,
        title: $model->title,
        // ... más propiedades
    );
}
```

## 🎨 Principios SOLID Aplicados

### 1. Single Responsibility Principle (SRP)
**"Una clase debe tener una sola razón para cambiar"**

✅ **Aplicación en el proyecto:**
- `ProjectController`: Solo maneja HTTP requests/responses
- `ProjectService`: Solo contiene lógica de negocio
- `EloquentProjectRepository`: Solo gestiona persistencia

```php
// ❌ MAL - Controller con múltiples responsabilidades
class ProjectController {
    public function index() {
        $data = DB::table('projects')->get(); // ❌ Acceso directo a DB
        // ... lógica de negocio ...
        return response()->json($data);
    }
}

// ✅ BIEN - Responsabilidades separadas
class ProjectController {
    public function index() {
        $projects = $this->projectService->getAllProjects();
        return response()->json($projects->map->toArray());
    }
}
```

### 2. Open/Closed Principle (OCP)
**"Abierto a extensión, cerrado a modificación"**

✅ **Aplicación en el proyecto:**
- Nuevas implementaciones de repositorios sin modificar servicios
- Nuevos tipos de proyectos sin cambiar código existente

```php
// Se puede añadir MongoProjectRepository sin modificar ProjectService
class MongoProjectRepository implements ProjectRepositoryInterface {
    public function findAll(): Collection { /* ... */ }
}

// Service binding en RepositoryServiceProvider
$this->app->bind(
    ProjectRepositoryInterface::class,
    EloquentProjectRepository::class // Fácil de cambiar
);
```

### 3. Liskov Substitution Principle (LSP)
**"Las subclases deben ser sustituibles por sus clases base"**

✅ **Aplicación en el proyecto:**
- Cualquier implementación de `ProjectRepositoryInterface` puede sustituir a otra
- El service funciona igual con Eloquent, Mongo o cualquier otra implementación

```php
// Ambas implementaciones son intercambiables
interface ProjectRepositoryInterface {
    public function findAll(): Collection;
    public function findById(int $id): ?Project;
}

class EloquentProjectRepository implements ProjectRepositoryInterface { }
class MongoProjectRepository implements ProjectRepositoryInterface { }
class InMemoryProjectRepository implements ProjectRepositoryInterface { }
```

### 4. Interface Segregation Principle (ISP)
**"Los clientes no deben depender de interfaces que no usan"**

✅ **Aplicación en el proyecto:**
- Interfaces pequeñas y específicas
- Solo métodos necesarios

```php
// ❌ MAL - Interface "gorda" con métodos no usados
interface ProjectRepositoryInterface {
    public function findAll();
    public function findById(int $id);
    public function save(Project $project);
    public function delete(int $id);
    public function sendEmail(Project $project);      // ❌
    public function generatePDF(Project $project);    // ❌
    public function exportToExcel(Collection $projects); // ❌
}

// ✅ BIEN - Interface específica
interface ProjectRepositoryInterface {
    public function findAll(): Collection;
    public function findById(int $id): ?Project;
    public function save(Project $project): Project;
    public function delete(int $id): bool;
}
```

### 5. Dependency Inversion Principle (DIP)
**"Depender de abstracciones, no de implementaciones"**

✅ **Aplicación en el proyecto:**
- Services dependen de interfaces, no de implementaciones concretas
- Inversión de control mediante inyección de dependencias

```php
// ❌ MAL - Dependencia de implementación concreta
class ProjectService {
    private EloquentProjectRepository $repository;
    
    public function __construct() {
        $this->repository = new EloquentProjectRepository(); // ❌
    }
}

// ✅ BIEN - Dependencia de abstracción
class ProjectService {
    public function __construct(
        private readonly ProjectRepositoryInterface $repository // ✅
    ) {}
}
```

## 🧪 Testing Strategy

### Tests Unitarios (Application Layer)
Prueban la lógica de negocio en aislamiento usando **mocks**:

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

### Tests de Integración (Presentation Layer)
Prueban el flujo completo de la aplicación:

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

## 🎨 Frontend Architecture (Vue.js)

### Composition API Pattern

```javascript
// Composable: Lógica reutilizable
export function useBusMap() {
  const zoom = ref(10);
  
  const getResponsiveZoom = () => {
    const width = window?.innerWidth ?? 1920;
    return width < 640 ? 9.5 : 10.5;
  };
  
  return { zoom, getResponsiveZoom };
}

// Component: UI y presentación
<script setup>
import { useBusMap } from '@/composables/useBusMap';

const { zoom, getResponsiveZoom } = useBusMap();
</script>
```

### Component Structure

```
components/
├── Common/              # Componentes reutilizables
│   ├── StatsCard.vue   # Props tipadas, slots, eventos
│   ├── LoadingSpinner.vue
│   └── InfoBanner.vue
├── Projects/            # Específicos de dominio
│   └── ProjectCard.vue
└── Layout/              # Estructura de página
    ├── Header.vue
    └── Footer.vue
```

## 🚀 Ventajas de esta Arquitectura

### ✅ Mantenibilidad
- Código organizado por capas lógicas
- Fácil de entender y navegar
- Cambios localizados en una capa

### ✅ Testabilidad
- Tests unitarios simples con mocks
- Tests de integración completos
- Alta cobertura de código

### ✅ Escalabilidad
- Fácil añadir nuevas features
- Nuevas implementaciones sin cambios
- Código preparado para crecer

### ✅ Flexibilidad
- Cambiar de ORM (Eloquent → Doctrine)
- Cambiar de BD (MySQL → MongoDB)
- Cambiar de cache (Redis → Memcached)

### ✅ Independencia
- Lógica de negocio sin Laravel
- Testeable sin framework
- Portable a otros proyectos

## 📚 Referencias

- [Hexagonal Architecture](https://alistair.cockburn.us/hexagonal-architecture/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Clean Architecture - Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)

---

<div align="center">
  <p><strong>Architecture Documentation</strong></p>
  <p>Last updated: November 2025</p>
</div>
