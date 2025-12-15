# Carlos Miguel Ortega Arencibia's Portfolio

<p align="center">
  <a href="README.es.md">Español</a> |
  <a href="README.md">English</a>
</p>

[![Laravel](https://img.shields.io/badge/Laravel-12.39-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5.25-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3.28-777BB4?style=flat&logo=php)](https://php.net)
[![Node.js](https://img.shields.io/badge/Node.js-24.11.1-339933?style=flat&logo=node.js)](https://nodejs.org)
[![Tests](https://img.shields.io/badge/Tests-110%20passing-success?style=flat)](https://github.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Website](https://img.shields.io/badge/Website-devortega.com-blue?style=flat&logo=googlechrome)](https://devortega.com)

**Live Demo**: [https://devortega.com](https://devortega.com)

A modern, professional portfolio built with cutting-edge PHP and JavaScript technologies. Showcases my experience, skills, education, and featured projects, following hexagonal architecture and SOLID principles.

## Tech Stack

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

## Key Features

- **Modern Interface**: Fully responsive dark design with smooth animations and gradients
- **RESTful API**: Robust backend with endpoints for projects, experience, skills, and education
- **API Documentation**: Integrated Swagger UI at `/api/documentation`
- **Comprehensive Testing**: 110 tests (21 backend + 89 frontend) with full coverage
- **Hexagonal Architecture**: Clean, maintainable, and scalable code following SOLID principles
- **Internationalization**: Full multi-language support (ES/EN) with Vue i18n for UI and database content
- **SPA**: Seamless navigation without reloads thanks to Vue Router
- **Interactive Demos**: Featured projects with interactive maps and advanced features
- **Performance**: Optimized with Vite and lazy loading
- **Type Safety**: PHP 8.3 with strict types and modern JavaScript best practices
- **Advanced Filtering**: Year-based filters for skills, experience, and education
- **Docker Ready**: Multi-stage Dockerfile optimized for production with PHP-FPM + Nginx

## Quick Start

### Prerequisites

- PHP >= 8.3
- Node.js >= 24.x LTS
- npm >= 11.x
- Composer
- SQLite or MySQL/PostgreSQL
- Python 3.x with `rasterio` and `gdal-bin` (for Hiking demo)

### Quick Installation (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/DevOrtega/portfolio.git
cd portfolio

# 2. Run complete setup (installs everything and configures DB)
composer setup

# 3. Start development environment
composer dev
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

### Available Scripts

| Command | When to use |
|---------|-------------|
| `composer setup` | First installation after cloning the project |
| `composer dev` | Start development environment (server + vite + logs + queue) |
| `composer refresh` | After `git pull` with changes in dependencies, migrations, or seeders |
| `composer test` | Run backend tests |

### Manual Installation (Step by Step)

<details>
<summary>Click to expand manual installation</summary>

1. **Clone the repository**
   ```bash
   git clone https://github.com/DevOrtega/portfolio.git
   cd portfolio
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database**
   Configure your database in the `.env` file (SQLite by default). Then run migrations and seeders:
   ```bash
   touch database/database.sqlite # If using SQLite
   php artisan migrate:fresh --seed
   ```

6. **Generate API documentation**
   ```bash
   php artisan l5-swagger:generate
   ```

7. **Run tests (optional but recommended)**
   ```bash
   php artisan test    # Backend tests
   npm test           # Frontend tests
   ```

8. **Run development servers**
   
   You'll need two terminals:

   *Terminal 1 (Backend):*
   ```bash
   php artisan serve
   ```

   *Terminal 2 (Frontend):*
   ```bash
   npm run dev
   ```

9. **View the project**
   Open your browser at [http://localhost:8000](http://localhost:8000).

</details>

## API Endpoints

Full documentation available at [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

### Main Endpoints

| Method | Endpoint | Parameters | Description |
|--------|----------|------------|-------------|
| GET | `/api/personal-info` | - | Personal information (bio, contact, social media) |
| GET | `/api/projects` | - | List of featured projects with full details |
| GET | `/api/experiences` | `?year=2023` (optional) | Work history sorted by date, filterable by year |
| GET | `/api/education` | `?year=2023` (optional) | Academic background, filterable by year |
| GET | `/api/skills` | `?year=2023` (optional) | Technical skills categorized, filterable by experience year |
| GET | `/api/bus/data` | - | Bus tracking data (companies, lines, stops, routes) |
| GET | `/api/hiking/route` | `?start=lat,lon&end=lat,lon` | Hiking route with 3D elevation profile |

### Response Example

```json
// GET /api/projects
[
  {
    "id": 1,
    "title": "ATM Manager",
    "description": "ATM network management system with real-time monitoring",
    "image": "/images/atm-manager.jpg",
    "tags": ["Laravel", "Vue.js", "MySQL", "Docker"],
    "github_url": "https://github.com/DevOrtega/atm-manager",
    "url": "/demo/atm-manager"
  }
]
```

## Interactive Demos

The portfolio includes functional demos of real projects:

### Real-Time Bus Tracking (GuaguasTracker)
- **Route**: `/projects/demo/guaguas-tracker`
- **Technologies**: Vue 3, Leaflet, OSRM API, Composables, SQLite
- **Features**:
  - Interactive map of Gran Canaria with real routes
  - Bus movement following real roads using OSRM (Open Source Routing Machine)
  - Custom bus icons by company (Guaguas Municipales, Global, Night Lines)
  - Service schedules (urban/interurban/night)
  - Responsive system with adaptive zoom
  - Geographic boundary detection
  - Smooth movement interpolation between route points
  - **NEW**: Database-backed with hexagonal architecture
  - **NEW**: API endpoint for bus data (`/api/bus/data`)

### Hiking Route Planner
- **Route**: `/projects/demo/hiking-planner`
- **Technologies**: Laravel (PHP), Python (GDAL/Rasterio), Leaflet
- **Features**:
  - Calculation of hiking routes between two points
  - Integration with OSRM (Open Source Routing Machine) for trails
  - **3D Elevation**: Uses local Digital Terrain Models (DTM/MDT) to calculate precise altitude
  - Elevation profiles (gain/loss stats)
  - Hybrid PHP+Python architecture for geospatial processing

### ATM Manager
- **Route**: `/projects/demo/atm-manager`
- **Technologies**: Vue 3, Element Plus, Pinia
- **Features**: (In development)

### Upcoming Demos
- Enterprise ERP system
- University researchers portal
- Google Assistant chatbot

## Project Architecture

This project follows **SOLID** principles and is structured using **Hexagonal Architecture** (Ports & Adapters), ensuring:
- **Maintainability**: Organized and easy-to-understand code
- **Testability**: Decoupled and easy-to-test components
- **Scalability**: Easy to extend without modifying existing code
- **Flexibility**: Ability to change implementations without affecting business logic

### Architecture Layers

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

### Directory Structure

```
app/
├── Domain/                          # Domain Layer
│   ├── Portfolio/
│   │   ├── Entities/                # Domain entities (Project, Skill, etc.)
│   │   └── Repositories/            # Repository interfaces (contracts)
│   └── Bus/
│       ├── Entities/                # Bus domain entities (BusCompany, BusLine, BusStop)
│       └── Repositories/            # Bus repository interfaces
│
├── Application/                     # Application Layer
│   ├── Portfolio/
│   │   ├── Services/                # Application services (business logic)
│   │   └── DTOs/                    # Data Transfer Objects
│   └── Bus/
│       └── Services/                # Bus application services (BusService)
│
├── Infrastructure/                  # Infrastructure Layer
│   └── Persistence/
│       └── Eloquent/
│           ├── Models/              # Eloquent models (ProjectModel, BusLineModel, etc.)
│           └── Repositories/        # Repository implementations
│
└── Http/                            # Presentation Layer
    └── Controllers/
        └── Api/                     # API controllers (HTTP only)
```

For detailed architecture documentation, see [ARCHITECTURE.md](ARCHITECTURE.md).

## Testing

The project includes a comprehensive test suite using **PEST** (backend) and **Vitest** (frontend).

### Test Statistics

- **Backend**: 21 tests, 155 assertions
- **Frontend**: 89 tests (6 components + 2 composables)
- **Total**: 110 tests passing
- **Coverage**: High coverage across all layers

### Running Tests

```bash
# Backend tests (PEST)
php artisan test
php artisan test --filter=ProjectServiceTest
php artisan test --coverage

# Frontend tests (Vitest)
npm test
npm run test:ui        # Interactive UI
npm run test:coverage  # With coverage report

# Run all tests
php artisan test && npm test
```

## Performance

- **First Contentful Paint**: < 1.5s
- **Time to Interactive**: < 3s
- **Lighthouse Score**: 95+
- **Optimized Build**: Automatic code splitting
- **Lazy Loading**: Components and routes loaded on demand

## Security

- CSRF Protection enabled
- XSS Prevention with sanitization
- SQL Injection prevention with Eloquent ORM
- Rate limiting on API endpoints
- Secure headers configured
- Input validation on all endpoints

## Deployment

### Docker Compose Deployment (Recommended)

```bash
# Build and launch (first time or after changes)
docker compose up -d --build

# View real-time logs
docker compose logs -f

# Or use the included script
chmod +x deploy-compose.sh
./deploy-compose.sh build
```

For detailed deployment instructions, see [DEVELOPMENT.md](DEVELOPMENT.md).

## Documentation

| Document | Description |
|----------|-------------|
| [README.md](README.md) | Project overview (English) |
| [README.es.md](README.es.md) | Project overview (Spanish) |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Detailed architecture documentation |
| [DEVELOPMENT.md](DEVELOPMENT.md) | Development guide |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Contribution guidelines |
| [CHANGELOG.md](CHANGELOG.md) | Version history |

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Author

**Carlos Miguel Ortega Arencibia**

Full Stack Developer | Laravel & Vue.js Specialist

- **Web**: [devortega.com](https://www.devortega.com)
- **LinkedIn**: [linkedin.com/in/carlosmortega](https://www.linkedin.com/in/carlosmortega/)
- **Email**: carloso2103@gmail.com
- **GitHub**: [github.com/DevOrtega](https://github.com/DevOrtega)

## Acknowledgments

- Laravel Framework for providing a robust ecosystem
- Vue.js for elegant reactivity and composition
- Tailwind CSS for the utility-first design system
- The open source community for amazing tools

---

**Carlos Miguel Ortega Arencibia's Portfolio**

© 2025 - All rights reserved
