# Portfolio de Carlos Miguel Ortega Arencibia

<p align="center">
  <a href="README.es.md">🇪🇸 Español</a> •
  <a href="README.md">🇬🇧 English</a>
</p>

[![Laravel](https://img.shields.io/badge/Laravel-12.39-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5.25-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3.28-777BB4?style=flat&logo=php)](https://php.net)
[![Node.js](https://img.shields.io/badge/Node.js-24.11.1-339933?style=flat&logo=node.js)](https://nodejs.org)
[![Tests](https://img.shields.io/badge/Tests-110%20passing-success?style=flat)](https://github.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Website](https://img.shields.io/badge/Website-devortega.com-blue?style=flat&logo=googlechrome)](https://devortega.com)

**Demo en vivo**: [https://devortega.com](https://devortega.com)

Este proyecto es un portfolio personal moderno y profesional desarrollado con las últimas tecnologías del ecosistema PHP y JavaScript. Muestra mi experiencia, habilidades, educación y proyectos destacados, siguiendo arquitectura hexagonal y principios SOLID.

## Stack Tecnológico

### Backend
- **PHP**: 8.3.28 (Latest stable)
- **Laravel**: 12.39.0 (Framework)
- **Pest**: 3.8.4 (Testing Framework)
- **L5-Swagger**: 9.0.1 (API Documentation)
- **SQLite/MySQL**: Database (configurable)

### Frontend
- **Vue.js**: 3.5.25 (Composition API)
- **Node.js**: 24.11.1 LTS (Krypton)
- **npm**: 11.6.3
- **Vite**: 7.2.4 (Build tool)
- **Vitest**: 4.0.13 (Testing)
- **TailwindCSS**: 4.1.17 (Styling)
- **Vue Router**: 4.6.3 (SPA routing)
- **Pinia**: 3.0.4 (State management)
- **Vue i18n**: 11.2.1 (Internationalization)
- **Element Plus**: 2.11.8 (UI Components)
- **Leaflet**: 1.9.4 (Maps - for demos)
- **Axios**: 1.13.2 (HTTP client)

## Características Principales

- **Interfaz Moderna**: Diseño oscuro totalmente responsivo con animaciones suaves y gradientes
- **API RESTful**: Backend robusto con endpoints para proyectos, experiencia, habilidades y educación
- **Documentación API**: Swagger UI integrado en `/api/documentation`
- **Testing Completo**: 110 tests (21 backend + 89 frontend) con cobertura completa
- **Arquitectura Hexagonal**: Código limpio, mantenible y escalable siguiendo principios SOLID
- **Internacionalización**: Soporte multiidioma completo (ES/EN) con Vue i18n para UI y contenido de base de datos
- **SPA**: Navegación fluida sin recargas gracias a Vue Router
- **Demos Interactivas**: Proyectos destacados con mapas interactivos y funcionalidades avanzadas
- **Rendimiento**: Optimizado con Vite y lazy loading
- **Type Safety**: PHP 8.3 con tipos estrictos y JavaScript moderno con mejores prácticas
- **Filtrado Avanzado**: Filtros por año en aptitudes, experiencia y formación académica
- **Docker Ready**: Dockerfile multi-stage optimizado para producción con PHP-FPM + Nginx

## Instalación y Puesta en Marcha

### Prerrequisitos

- PHP >= 8.3
- Node.js >= 24.x LTS
- npm >= 11.x
- Composer
- SQLite o MySQL/PostgreSQL

### Instalación Rápida (Recomendado)

```bash
# 1. Clonar el repositorio
git clone https://github.com/DevOrtega/portfolio.git
cd portfolio

# 2. Ejecutar setup completo (instala todo y configura BD)
composer setup

# 3. Levantar entorno de desarrollo
composer dev
```

Abre [http://localhost:8000](http://localhost:8000) en tu navegador.

### Scripts Disponibles

| Comando | Cuándo usarlo |
|---------|---------------|
| `composer setup` | Primera instalación tras clonar el proyecto |
| `composer dev` | Levantar entorno de desarrollo (servidor + vite + logs + queue) |
| `composer refresh` | Tras `git pull` con cambios en dependencias, migraciones o seeders |
| `composer test` | Ejecutar tests de backend |

### Instalación Manual (Paso a Paso)

<details>
<summary>Click para expandir instalación manual</summary>

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/DevOrtega/portfolio.git
   cd portfolio
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de JavaScript**
   ```bash
   npm install
   ```

4. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Base de Datos**
   Configura tu base de datos en el archivo `.env` (por defecto usa SQLite). Luego ejecuta las migraciones y los seeders:
   ```bash
   touch database/database.sqlite # Si usas SQLite
   php artisan migrate:fresh --seed
   ```

6. **Generar documentación de la API**
   ```bash
   php artisan l5-swagger:generate
   ```

7. **Ejecutar tests (opcional pero recomendado)**
   ```bash
   php artisan test    # Backend tests
   npm test           # Frontend tests
   ```

8. **Ejecutar servidores de desarrollo**
   
   Necesitarás dos terminales:

   *Terminal 1 (Backend):*
   ```bash
   php artisan serve
   ```

   *Terminal 2 (Frontend):*
   ```bash
   npm run dev
   ```

9. **Ver el proyecto**
   Abre tu navegador en [http://localhost:8000](http://localhost:8000).

</details>

## Endpoints de la API

Documentación completa disponible en [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

### Endpoints Principales

| Método | Endpoint | Parámetros | Descripción |
|--------|----------|------------|-------------|
| GET | `/api/personal-info` | - | Información personal (bio, contacto, redes sociales) |
| GET | `/api/projects` | - | Lista de proyectos destacados con detalles completos |
| GET | `/api/experiences` | `?year=2023` (opcional) | Historial laboral ordenado por fecha, filtrable por año |
| GET | `/api/education` | `?year=2023` (opcional) | Formación académica, filtrable por año |
| GET | `/api/skills` | `?year=2023` (opcional) | Aptitudes técnicas categorizadas, filtrables por año de experiencia |

### Ejemplo de Respuesta

```json
// GET /api/projects
[
  {
    "id": 1,
    "title": "Gestor de Cajeros Automáticos",
    "description": "Sistema de gestión de red de ATMs con monitoreo en tiempo real",
    "image": "/images/atm-manager.jpg",
    "tags": ["Laravel", "Vue.js", "MySQL", "Docker"],
    "github_url": "https://github.com/DevOrtega/atm-manager",
    "url": "/demo/atm-manager"
  }
]
```

## Demos Interactivas

El portfolio incluye demos funcionales de proyectos reales:

### Seguimiento de Guaguas en Tiempo Real
- **Ruta**: `/projects/demo/guaguas-tracker`
- **Tecnologías**: Vue 3, Leaflet, OSRM API, Composables
- **Características**:
  - Mapa interactivo de Gran Canaria con rutas reales
  - Movimiento de buses siguiendo carreteras usando OSRM (Open Source Routing Machine)
  - Iconos personalizados de guaguas por compañía
  - Horarios de servicio (urbano/interurbano/nocturno)
  - Sistema responsive con zoom adaptativo
  - Detección de límites geográficos
  - Interpolación suave de movimiento entre puntos de ruta

### Gestor de Cajeros Automáticos
- **Ruta**: `/projects/demo/atm-manager`
- **Tecnologías**: Vue 3, Element Plus, Pinia
- **Características**: (En desarrollo)

### Próximas Demos
- Sistema ERP empresarial
- Portal de investigadores universitarios
- Chatbot con Google Assistant

## Arquitectura del Proyecto

Este proyecto sigue los principios **SOLID** y está estructurado usando **Arquitectura Hexagonal** (Ports & Adapters), lo que garantiza:
- **Mantenibilidad**: Código organizado y fácil de entender
- **Testabilidad**: Componentes desacoplados y fáciles de probar
- **Escalabilidad**: Fácil de extender sin modificar código existente
- **Flexibilidad**: Posibilidad de cambiar implementaciones sin afectar la lógica de negocio

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

#### 1. Single Responsibility Principle (SRP)
Cada clase tiene una única responsabilidad:
- **Controllers**: Solo manejan peticiones/respuestas HTTP
- **Services**: Solo contienen lógica de negocio
- **Repositories**: Solo gestionan persistencia de datos

#### 2. Open/Closed Principle (OCP)
El código está abierto a extensión pero cerrado a modificación:
- Nuevas implementaciones de repositorios pueden añadirse sin modificar servicios
- Uso de interfaces permite cambiar implementaciones fácilmente

#### 3. Liskov Substitution Principle (LSP)
Cualquier implementación de repositorio puede sustituir a otra:
- `EloquentProjectRepository` puede reemplazarse por `MongoProjectRepository`
- Los servicios funcionan con interfaces, no con implementaciones concretas

#### 4. Interface Segregation Principle (ISP)
Interfaces pequeñas y específicas:
- `ProjectRepositoryInterface` solo define métodos relacionados con proyectos
- No hay métodos innecesarios que los clientes no usen

#### 5. Dependency Inversion Principle (DIP)
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

## Testing

El proyecto cuenta con una suite completa de tests utilizando **PEST** (backend) y **Vitest** (frontend).

### Estadísticas de Tests

- **Backend**: 21 tests, 155 assertions
- **Frontend**: 89 tests (6 componentes + 2 composables)
- **Total**: 110 tests pasando
- **Cobertura**: Alta cobertura en todas las capas

### Ejecutar Tests

```bash
# Backend tests (PEST)
php artisan test
php artisan test --filter=ProjectServiceTest
php artisan test --coverage

# Frontend tests (Vitest)
npm test
npm run test:ui        # UI interactiva
npm run test:coverage  # Con reporte de cobertura

# Ejecutar todos los tests
php artisan test && npm test
```

### Tipos de Tests

#### Backend Tests (`tests/`)

**Tests Unitarios** (`tests/Unit/`):
- ProjectService: Lógica de negocio con mocks

**Tests de Integración** (`tests/Feature/Api/`):
- PersonalInfoApiTest: GET endpoint, 404 handling
- SkillApiTest: Ordering, empty arrays, proficiency
- ExperienceApiTest: Date ordering, null handling
- EducationApiTest: Ordering, ongoing education
- ProjectApiTest: CRUD operations, data structure

#### Frontend Tests (`resources/js/`)

**Componentes** (`__tests__/`):
- StatsCard: Props, color variants, hover effects
- LoadingSpinner: Sizes, accessibility
- InfoBanner: Types, icons, slots
- ProjectCard: Images, links, RouterLink
- TimelineItem: Colors, timeline dot
- SectionHeader: Icons, styling

**Composables** (`composables/__tests__/`):
- useBusMap: Geographic bounds, Leaflet icons
- useBusSchedule: Service schedules, time logic

## Scripts Disponibles

### Backend (PHP)
```bash
composer install          # Instalar dependencias
php artisan serve        # Servidor de desarrollo
php artisan test         # Ejecutar tests
php artisan migrate      # Ejecutar migraciones
php artisan db:seed      # Poblar base de datos
php artisan l5-swagger:generate  # Generar documentación API
php artisan pint         # Formatear código
```

### Frontend (JavaScript)
```bash
npm install              # Instalar dependencias
npm run dev             # Servidor de desarrollo con HMR
npm run build           # Build para producción
npm test                # Ejecutar tests
npm run test:ui         # Tests con interfaz visual
npm run test:coverage   # Tests con cobertura
```

## Tecnologías Modernas Utilizadas

### PHP 8.3 Features
- Typed properties en todos los modelos
- Constructor property promotion
- Readonly classes para inmutabilidad
- Final classes para mejor rendimiento
- Enums para valores constantes
- Modern array functions

### JavaScript ES2024
- Nullish coalescing operator (`??`)
- Optional chaining (`?.`)
- Async/await para operaciones asíncronas
- ES Modules
- Composition API de Vue 3
- Reactive state management con Pinia

## Rendimiento

- **First Contentful Paint**: < 1.5s
- **Time to Interactive**: < 3s
- **Lighthouse Score**: 95+
- **Build optimizado**: Code splitting automático
- **Lazy loading**: Componentes y rutas cargadas bajo demanda

## Seguridad

- CSRF Protection activado
- XSS Prevention con sanitización
- SQL Injection prevention con Eloquent ORM
- Rate limiting en API endpoints
- Secure headers configurados
- Input validation en todos los endpoints

## Despliegue

### Despliegue con Docker Compose (Recomendado)

El proyecto incluye `docker-compose.yml` para un despliegue simple y rápido:

```bash
# Construir y lanzar (primera vez o después de cambios)
docker compose up -d --build

# Reiniciar sin reconstruir
docker compose restart

# Ver logs en tiempo real
docker compose logs -f

# Parar servicios
docker compose down

# O usar el script incluido
chmod +x deploy-compose.sh
./deploy-compose.sh build    # Primera vez
./deploy-compose.sh restart  # Reinicio rápido
./deploy-compose.sh logs     # Ver logs
```

### Actualizar Despliegue tras git pull

Cuando traes cambios del repositorio a tu servidor/VM:

```bash
# 1. Traer los cambios
git pull

# 2. Reconstruir y reiniciar (siempre seguro)
docker compose up -d --build
```

| Tipo de cambio | Comando necesario |
|----------------|-------------------|
| Solo código PHP (sin dependencias) | `docker compose restart` (rápido) |
| Nuevas dependencias composer/npm | `docker compose up -d --build` |
| Cambios en migraciones | `docker compose up -d --build` |
| Cambios en Dockerfile | `docker compose up -d --build` |
| Cambios en .env | `docker compose up -d` |

**Nota**: Ante la duda, usa siempre `docker compose up -d --build`. Tarda un poco más pero garantiza que todo está actualizado.

**Características del docker-compose.yml:**
- Volúmenes persistentes para storage y database
- Health checks automáticos
- Restart automático si el contenedor falla
- Red aislada para seguridad
- Variables de entorno configurables
- Puerto 80 expuesto (mapea a 8080 interno)
- Límites de recursos (CPU y RAM)

### Comandos Útiles Docker Compose

```bash
# Gestión del contenedor
docker compose ps                    # Ver estado de servicios
docker compose logs -f              # Ver logs en tiempo real
docker compose logs -f --tail 50    # Ver últimos 50 logs
docker compose restart              # Reiniciar servicios
docker compose down                 # Parar y eliminar contenedores
docker compose down -v              # Parar y eliminar volúmenes

# Ejecutar comandos dentro del contenedor
docker compose exec portfolio bash                           # Abrir shell interactiva
docker compose exec portfolio php artisan migrate           # Ejecutar migraciones
docker compose exec portfolio php artisan cache:clear       # Limpiar cache
docker compose exec portfolio php artisan config:cache      # Cachear configuración
docker compose exec portfolio php artisan route:list        # Listar rutas
docker compose exec portfolio php artisan tinker            # Abrir REPL de Laravel
docker compose exec portfolio php artisan test              # Ejecutar tests
docker compose exec portfolio composer install              # Instalar dependencias
docker compose exec portfolio tail -f storage/logs/laravel.log  # Ver logs de Laravel

# Inspección y debugging
docker compose exec portfolio env                           # Ver variables de entorno
docker compose exec portfolio cat .env                      # Ver archivo .env
docker compose exec portfolio ls -la storage/logs          # Listar archivos de logs
docker compose exec portfolio php -v                        # Ver versión de PHP
docker compose exec portfolio node -v                       # Ver versión de Node.js

# Gestión de recursos
docker compose top                   # Ver procesos corriendo
docker stats portfolio              # Ver uso de CPU/RAM en tiempo real
```

### Script de Despliegue Automatizado

El proyecto incluye `deploy-compose.sh` para facilitar operaciones comunes:

```bash
chmod +x deploy-compose.sh

./deploy-compose.sh build    # Construir y lanzar (primera vez o tras cambios)
./deploy-compose.sh restart  # Reinicio rápido sin rebuild
./deploy-compose.sh logs     # Ver logs en tiempo real
./deploy-compose.sh stop     # Parar servicios
./deploy-compose.sh clean    # Eliminar todo (contenedores, volúmenes, imágenes)
```

### Despliegue con Docker (Sin Compose)

También puedes usar Docker directamente:

```bash
# Construir la imagen
docker build -t portfolio .

# Ejecutar el contenedor
docker run -d -p 80:8080 \
  --name portfolio \
  --health-cmd="curl -f http://localhost:8080/up || exit 1" \
  --health-interval=30s \
  --health-timeout=10s \
  --health-retries=3 \
  portfolio
```

**El Dockerfile incluye:**
- PHP 8.3 con FPM + Nginx (serversideup/php:8.3-fpm-nginx)
- Node.js 20 para build de assets
- Extensiones PHP necesarias (bcmath, intl)
- Generación automática de APP_KEY
- Migraciones y seeders automáticos
- Build de assets optimizado para producción
- Caché de configuración (config, routes, views)
- Health check en `/up`
- Permisos correctos para www-data

### Plataformas Soportadas

El proyecto está preparado para despliegue en:
- **Contenedores**: Docker, Kubernetes, Proxmox LXC
- **Cloud**: AWS, DigitalOcean, Google Cloud, Azure
- **PaaS**: Laravel Forge, Railway, Render
- **Self-hosted**: VPS con Docker, Cloudflare Tunnel
- **Database**: SQLite (incluida), MySQL, PostgreSQL

### Build para Producción
```bash
# Backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
npm run build
```

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## Autor

**Carlos Miguel Ortega Arencibia**

Full Stack Developer | Laravel & Vue.js Specialist

- **Web**: [devortega.com](https://www.devortega.com)
- **LinkedIn**: [linkedin.com/in/carlosmortega](https://www.linkedin.com/in/carlosmortega/)
- **Email**: carloso2103@gmail.com
- **GitHub**: [github.com/DevOrtega](https://github.com/DevOrtega)

## Agradecimientos

- Laravel Framework por proporcionar un ecosistema robusto
- Vue.js por la reactividad y composición elegante
- Tailwind CSS por el sistema de diseño utility-first
- La comunidad open source por las increíbles herramientas

---

**Portfolio de Carlos Miguel Ortega Arencibia**

© 2025 - Todos los derechos reservados
