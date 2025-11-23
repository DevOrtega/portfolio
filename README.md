# Portfolio de Carlos Miguel Ortega Arencibia

Este proyecto es un portfolio personal moderno y "premium" desarrollado con las últimas tecnologías del ecosistema PHP y JavaScript. Muestra mi experiencia, habilidades, educación y proyectos destacados.

## 🚀 Tecnologías Utilizadas

-   **Backend**: [Laravel 11](https://laravel.com) (PHP)
-   **Frontend**: [Vue.js 3](https://vuejs.org) (Composition API)
-   **Build Tool**: [Vite](https://vitejs.dev)
-   **Estilos**: [TailwindCSS](https://tailwindcss.com)
-   **API Documentation**: [OpenAPI/Swagger](https://swagger.io) (L5-Swagger)
-   **Base de Datos**: SQLite (Configurable a MySQL/PostgreSQL)

## ✨ Características Principales

-   **Diseño Premium**: Interfaz oscura, moderna y responsiva con animaciones suaves.
-   **API RESTful**: Backend robusto que sirve los datos del portfolio (Proyectos, Experiencia, Habilidades, Educación).
-   **Documentación API**: Swagger UI integrado para explorar los endpoints (`/api/documentation`).
-   **Gestión de Contenido**: Base de datos poblada mediante Seeders con información real extraída de CV y LinkedIn.
-   **SPA (Single Page Application)**: Navegación fluida sin recargas de página gracias a Vue Router.

## 🛠️ Instalación y Puesta en Marcha

Sigue estos pasos para ejecutar el proyecto en tu entorno local:

1.  **Clonar el repositorio**
    ```bash
    git clone <url-del-repositorio>
    cd portfolio
    ```

2.  **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de JavaScript**
    ```bash
    npm install
    ```

4.  **Configurar entorno**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Base de Datos**
    Configura tu base de datos en el archivo `.env` (por defecto usa SQLite). Luego ejecuta las migraciones y los seeders:
    ```bash
    touch database/database.sqlite # Si usas SQLite
    php artisan migrate:refresh --seed
    ```

6.  **Generar documentación de la API**
    ```bash
    php artisan l5-swagger:generate
    ```

7.  **Ejecutar servidores de desarrollo**
    Necesitarás dos terminales:

    *Terminal 1 (Backend):*
    ```bash
    php artisan serve
    ```

    *Terminal 2 (Frontend):*
    ```bash
    npm run dev
    ```

8.  **Ver el proyecto**
    Abre tu navegador en [http://localhost:8000](http://localhost:8000).

## 📚 Endpoints de la API

Puedes ver la documentación completa en [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation).

-   `GET /api/personal-info`: Información personal (Bio, Redes, etc.)
-   `GET /api/projects`: Lista de proyectos destacados.
-   `GET /api/experiences`: Historial laboral.
-   `GET /api/education`: Formación académica.
-   `GET /api/skills`: Habilidades técnicas categorizadas.

## 🏗️ Arquitectura del Proyecto

Este proyecto sigue los principios **SOLID** y está estructurado usando **Arquitectura Hexagonal** (Ports & Adapters), lo que garantiza:
- ✅ **Mantenibilidad**: Código organizado y fácil de entender
- ✅ **Testabilidad**: Componentes desacoplados y fáciles de probar
- ✅ **Escalabilidad**: Fácil de extender sin modificar código existente
- ✅ **Flexibilidad**: Posibilidad de cambiar implementaciones sin afectar la lógica de negocio

### Capas de la Arquitectura

```
┌─────────────────────────────────────────────────────────────┐
│                  Presentation Layer                          │
│              (Controllers, API Resources)                    │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports)
┌──────────────────────▼──────────────────────────────────────┐
│                 Application Layer                            │
│          (Services, Use Cases, DTOs)                         │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports)
┌──────────────────────▼──────────────────────────────────────┐
│                   Domain Layer                               │
│            (Entities, Value Objects)                         │
└──────────────────────┬──────────────────────────────────────┘
                       │ (Ports)
┌──────────────────────▼──────────────────────────────────────┐
│               Infrastructure Layer                           │
│      (Repositories, Database, External APIs)                 │
└─────────────────────────────────────────────────────────────┘
```

### Estructura de Directorios

```
app/
├── Domain/                          # Capa de Dominio
│   └── Portfolio/
│       ├── Entities/                # Entidades de dominio (Project, Skill, etc.)
│       └── Repositories/            # Interfaces de repositorios (contratos)
│
├── Application/                     # Capa de Aplicación
│   └── Portfolio/
│       ├── Services/                # Servicios de aplicación (lógica de negocio)
│       └── DTOs/                    # Data Transfer Objects
│
├── Infrastructure/                  # Capa de Infraestructura
│   └── Persistence/
│       └── Eloquent/
│           ├── Models/              # Modelos Eloquent (ProjectModel, etc.)
│           └── Repositories/        # Implementaciones de repositorios
│
└── Http/                            # Capa de Presentación
    └── Controllers/
        └── Api/                     # Controladores API (solo HTTP)
```

### Principios SOLID Aplicados

#### 1. **Single Responsibility Principle (SRP)**
Cada clase tiene una única responsabilidad:
- **Controllers**: Solo manejan peticiones/respuestas HTTP
- **Services**: Solo contienen lógica de negocio
- **Repositories**: Solo gestionan persistencia de datos

#### 2. **Open/Closed Principle (OCP)**
El código está abierto a extensión pero cerrado a modificación:
- Nuevas implementaciones de repositorios pueden añadirse sin modificar servicios
- Uso de interfaces permite cambiar implementaciones fácilmente

#### 3. **Liskov Substitution Principle (LSP)**
Cualquier implementación de repositorio puede sustituir a otra:
- `EloquentProjectRepository` puede reemplazarse por `MongoProjectRepository`
- Los servicios funcionan con interfaces, no con implementaciones concretas

#### 4. **Interface Segregation Principle (ISP)**
Interfaces pequeñas y específicas:
- `ProjectRepositoryInterface` solo define métodos relacionados con proyectos
- No hay métodos innecesarios que los clientes no usen

#### 5. **Dependency Inversion Principle (DIP)**
Módulos de alto nivel no dependen de módulos de bajo nivel:
- `ProjectService` depende de `ProjectRepositoryInterface` (abstracción)
- No depende directamente de `EloquentProjectRepository` (implementación)

### Ejemplo de Flujo de Datos

```php
// 1. HTTP Request llega al Controller
ProjectController::index()

// 2. Controller delega al Service
$projects = $this->projectService->getAllProjects()

// 3. Service usa el Repository (a través de la interfaz)
return $this->repository->findAll()

// 4. Repository (Eloquent) consulta la base de datos
ProjectModel::all()->map(fn($m) => $this->toDomain($m))

// 5. Se devuelven entidades de dominio (Project)
// 6. Controller formatea la respuesta JSON
```

## 🧪 Testing con PEST

El proyecto utiliza **PEST** como framework de testing, siguiendo las mejores prácticas de TDD.

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=ProjectServiceTest
php artisan test --filter=ProjectApiTest

# Con cobertura
php artisan test --coverage
```

### Tipos de Tests

#### Tests Unitarios (`tests/Unit/`)
Prueban la lógica de negocio de forma aislada usando **mocks**:

```php
it('retrieves all projects', function () {
    $repository = Mockery::mock(ProjectRepositoryInterface::class);
    $repository->shouldReceive('findAll')->once()->andReturn(collect([...]));
    
    $service = new ProjectService($repository);
    $result = $service->getAllProjects();
    
    expect($result)->toHaveCount(3);
});
```

#### Tests de Integración (`tests/Feature/`)
Prueban el flujo completo de la aplicación (API endpoints):

```php
it('returns all projects via API', function () {
    ProjectModel::factory()->count(5)->create();
    
    $response = $this->getJson('/api/projects');
    
    $response->assertStatus(200)->assertJsonCount(5);
});
```

### Cobertura de Tests

- ✅ **Domain Layer**: Entidades y lógica de dominio
- ✅ **Application Layer**: Servicios y casos de uso
- ✅ **Infrastructure Layer**: Repositorios y persistencia
- ✅ **Presentation Layer**: Controladores y endpoints API

## 👤 Autor

**Carlos Miguel Ortega Arencibia**
-   [LinkedIn](https://www.linkedin.com/in/carlosmortega/)
-   [GitHub](https://github.com/DevOrtega)
