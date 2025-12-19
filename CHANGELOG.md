# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Major Updates

#### Hiking Planner - Advanced Features & Architecture
- **Points of Interest (POIs)**: Integration with Overpass API to find amenities (food, water, shelter) and natural features (peaks, viewpoints) along the route.
- **Interactive Planning**: Ability to add any discovered POI directly to the route, auto-recalculating the optimal path.
- **Hexagonal Architecture Refactor**: Complete refactor of the Hiking domain introducing Value Objects (`Coordinate`, `RouteGeometry`) and decoupling Infrastructure (`OverpassPoiProvider`) from Application logic.
- **Enhanced UI**: Added map legend, detailed popups with naming fallback logic, and loading indicators.

#### Portfolio Domain - Hexagonal Architecture Migration
- **Domain Entities**: Added `Experience`, `Education`, `Skill`, `PersonalInfo` entities
- **Repository Pattern**: Implemented repository interfaces and Eloquent implementations for all Portfolio entities
- **Application Services**: Added services for Experience, Education, Skill, and PersonalInfo
- **Controllers Refactored**: All Portfolio controllers now use dependency injection with services

### Added

#### Backend
- `app/Domain/Portfolio/Entities/Experience.php` - Work experience domain entity
- `app/Domain/Portfolio/Entities/Education.php` - Education domain entity
- `app/Domain/Portfolio/Entities/Skill.php` - Skill domain entity
- `app/Domain/Portfolio/Entities/PersonalInfo.php` - Personal info domain entity
- `app/Domain/Portfolio/Repositories/*Interface.php` - Repository interfaces for all entities
- `app/Application/Portfolio/Services/ExperienceService.php` - Experience application service
- `app/Application/Portfolio/Services/EducationService.php` - Education application service
- `app/Application/Portfolio/Services/SkillService.php` - Skill application service
- `app/Application/Portfolio/Services/PersonalInfoService.php` - Personal info application service
- `app/Infrastructure/Persistence/Eloquent/EloquentExperienceRepository.php`
- `app/Infrastructure/Persistence/Eloquent/EloquentEducationRepository.php`
- `app/Infrastructure/Persistence/Eloquent/EloquentSkillRepository.php`
- `app/Infrastructure/Persistence/Eloquent/EloquentPersonalInfoRepository.php`

### Changed
- `ExperienceController` - Now uses `ExperienceService` instead of direct Eloquent calls
- `EducationController` - Now uses `EducationService` instead of direct Eloquent calls
- `SkillController` - Now uses `SkillService` instead of direct Eloquent calls
- `PersonalInfoController` - Now uses `PersonalInfoService` instead of direct Eloquent calls
- `RepositoryServiceProvider` - Added bindings for all new repository interfaces
- Updated `ARCHITECTURE.md` and `ARCHITECTURE.en.md` to reflect complete hexagonal architecture

#### Frontend i18n
- Added complete translations for guaguas section in `es.js` and `en.js`
- Updated `BusLegend.vue`, `BusPopup.vue`, `GuaguasTracker.vue` to use `$t()` for all user-visible text

### Removed
- Removed unused `resources/js/data/guaguas/` folder (~4000 lines of hardcoded data)
- Removed unused `computed` import from `BusLinesTree.vue`

## [2.2.0] - 2025-12-10

### Major Updates

#### Model Context Protocol (MCP) Integration
- **MCP Server**: Implemented full MCP server capabilities within Laravel
- **Tools**: Added `osrm_get_route` tool, allowing AI agents to query real driving routes
- **Prompts**: Added expert prompts for Testing (`experto_pest`), Architecture (`experto_hexagonal`), SOLID (`experto_solid`), and Documentation
- **Infrastructure**: Added `McpServiceProvider` and `config/mcp.php` for seamless integration

#### Advanced Bus Tracking (OSRM)
- **Real-Road Routing**: Integrated OSRM (Open Source Routing Machine) to replace straight-line interpolation with real road geometry
- **OsrmService**: Implemented caching service for route optimization
- **Spatial Optimization**: Added spatial indexes to `bus_stops` for faster geographical queries

### Added
- `app/Providers/McpServiceProvider.php` - Service Provider for MCP tools and prompts
- `app/Infrastructure/Services/OsrmService.php` - Service for OSRM interaction
- `app/Http/Controllers/Api/BusRouteStatusController.php` - Controller for monitoring route status
- `database/migrations/2025_12_09_005345_create_mcp_sessions_table.php` - Migration for MCP sessions

### Changed
- **Internationalization**: Refactored `GuaguasTracker.vue` to remove hardcoded strings and use `useI18n`
- **Locales**: Added `lineFormat` key to `es.js` and `en.js` for consistent bus line display
- **Architecture**: Updated `OsrmService` to follow hexagonal architecture patterns (Infrastructure layer)

## [2.1.0] - 2025-12-05

### Major Updates

#### Bus Tracking System - Database Migration
- **Hexagonal Architecture**: Complete migration from hardcoded data to SQLite database
- **Domain Entities**: Added `BusCompany`, `BusLine`, `BusStop` entities
- **Repository Pattern**: Implemented repository interfaces and Eloquent implementations
- **Application Service**: Added `BusService` for orchestrating bus data operations
- **API Endpoint**: New `/api/bus/data` endpoint for fetching bus tracking data

#### Code Internationalization
- **Variable Naming**: Renamed all Spanish variables to English (`ida`/`vuelta` → `outbound`/`inbound`)
- **Comments**: Translated all Spanish comments to English throughout the codebase
- **API Keys**: Updated API response keys to English naming convention

#### Documentation Structure
- **Bilingual Documentation**: Added language selector to README files
- **README.md**: Now in English (international standard)
- **README.es.md**: Spanish version of README
- **Open Source Standards**: Following common patterns for multilingual documentation

### ✨ Added

#### Backend
- `app/Domain/Bus/Entities/BusCompany.php` - Bus company domain entity
- `app/Domain/Bus/Entities/BusLine.php` - Bus line domain entity with route coordinates
- `app/Domain/Bus/Entities/BusStop.php` - Bus stop domain entity with direction coordinates
- `app/Domain/Bus/Repositories/*Interface.php` - Repository interfaces for bus domain
- `app/Infrastructure/Persistence/Eloquent/Models/Bus*.php` - Eloquent models
- `app/Infrastructure/Persistence/Eloquent/Repositories/EloquentBus*Repository.php` - Repository implementations
- `app/Application/Bus/Services/BusService.php` - Application service
- `app/Http/Controllers/Api/BusController.php` - API controller
- `database/migrations/2025_12_05_*` - Bus-related migrations (4 tables)
- `database/seeders/BusDataSeeder.php` - Seeder with real Gran Canaria bus data

#### Frontend
- `resources/js/api/busApi.js` - API client for bus data
- `resources/js/api/index.js` - API module exports
- `resources/js/composables/useBusData.js` - Composable for bus data management

### Changed

#### Naming Convention Updates
- Database columns: `lat_ida`, `lng_ida`, `lat_vuelta`, `lng_vuelta` → `lat_outbound`, `lng_outbound`, `lat_inbound`, `lng_inbound`
- Route direction enum: `ida`, `vuelta` → `outbound`, `inbound`
- API response keys: `route_coords_ida/vuelta` → `route_coords_outbound/inbound`
- API response keys: `stops_ida/vuelta` → `stops_outbound/inbound`
- Entity properties: `stopsIda/stopsVuelta` → `stopsOutbound/stopsInbound`
- Frontend variables: Updated to match new API keys

#### Components Updated
- `GuaguasTracker.vue` - Updated API key references and translated comments
- `BusPopup.vue` - Translated direction labels (`Outbound`/`Inbound`)
- `BusLegend.vue` - Updated fallback texts to English
- `useBusMap.js` - Translated comments and labels
- `useBusSelection.js` - Already in English, verified

### Documentation
- Added language selector to README files
- Created README.es.md (Spanish version)
- Updated README.md to English
- Updated CHANGELOG.md with detailed changes

## [2.0.0] - 2025-11-24

### Major Updates

#### System Upgrades
- **PHP**: Upgraded from 8.2.29 to 8.3.28 (latest stable)
- **Node.js**: Upgraded from 20.19.5 to 24.11.1 LTS (Krypton)
- **npm**: Upgraded from 10.8.2 to 11.6.3
- **Laravel**: Updated to 12.39.0
- **Vue.js**: Updated to 3.5.25
- **vue-i18n**: Major upgrade from 9.14.5 to 11.2.1

#### Dependencies Updates
- Vite: 7.0.7 → 7.2.4
- TailwindCSS: Updated to 4.1.17
- Vitest: Updated to 4.0.13
- All PHP and npm packages updated to latest compatible versions

### Added

#### Features
- **Comprehensive Testing Suite**:
  - 21 backend tests (PHPUnit/Pest)
  - 89 frontend tests (Vitest)
  - Test coverage for all layers
  - Added test scripts: `test`, `test:ui`, `test:coverage`

- **Interactive Demos**:
  - GuaguasTracker: Real-time bus tracking with Leaflet maps
  - ATM Manager: ATM network management system (in development)
  - Upcoming demos section in projects view

- **Reusable Vue Components**:
  - `StatsCard`: Statistics display with hover effects
  - `LoadingSpinner`: Multiple sizes and accessibility
  - `InfoBanner`: Type variants (info/warning/error/success)
  - `ProjectCard`: Project display with image fallback
  - `TimelineItem`: Timeline visualization with colors
  - `SectionHeader`: Section headers with icons

- **Vue Composables**:
  - `useBusMap`: Map utilities, bounds checking, Leaflet icons
  - `useBusSchedule`: Service schedule logic and time management

- **Internationalization**:
  - 48 new translation keys in Spanish (es.json)
  - 48 new translation keys in English (en.json)
  - UI elements, navigation, categories, and settings

#### Documentation
- **README.md**: Complete rewrite with:
  - Technology badges and versions
  - Detailed stack information
  - Interactive demos section
  - API endpoints table
  - Testing documentation
  - Performance metrics
  - Security features
  - Deployment guide
  
- **ARCHITECTURE.md**: New comprehensive documentation:
  - Hexagonal Architecture explanation
  - Four-layer architecture diagram
  - SOLID principles with examples
  - Testing strategy
  - Frontend patterns
  - Code examples

- **API Documentation**: Enhanced Swagger/OpenAPI annotations

### Changed

#### Code Quality
- **PHP 8.3 Modernization**:
  - All models marked as `final` for better performance
  - Added PHPDoc type annotations for all properties
  - Implemented `casts()` method for type safety
  - All API controllers marked as `final`
  - Enhanced type safety throughout backend

- **JavaScript Modernization**:
  - Nullish coalescing operator (`??`) instead of `||`
  - Optional chaining (`?.`) for safer property access
  - Early returns for cleaner conditional logic
  - Better window object safety checks
  - Modern ES2024 patterns

- **Component Refactoring**:
  - Extracted reusable components from GuaguasTracker
  - Improved component composition and reusability
  - Better prop validation and typing
  - Added JSDoc documentation

#### UI/UX Improvements
- **Home View**:
  - Paragraph separation in bio with proper line spacing
  - Better typography and readability
  
- **Projects View**:
  - Cleaned up project database (removed old projects)
  - Added "Upcoming Projects" section with 3 demos
  - Styled with dashed borders and "in progress" indicators

- **Better Error Handling**:
  - Improved 404 responses
  - Better validation messages
  - Enhanced error states in UI

### Fixed

- Fixed model fillable properties for mass assignment
- Resolved SQLite driver requirement for PHP 8.3
- Fixed test assertions for updated models
- Corrected ProjectCard RouterLink integration
- Fixed TimelineItem badge Tailwind class handling

### Removed

- Removed obsolete projects from database
- Cleaned up unused imports in controllers
- Removed redundant code in components

### Configuration

- **nvm Installation**: Added for flexible Node.js version management
- **Vitest Configuration**: 
  - Vue plugin with happy-dom environment
  - Coverage reporting with v8 provider
  - Path aliases configured
  
- **Test Scripts**:
  - `npm test`: Run tests in CI mode
  - `npm run test:ui`: Interactive test UI
  - `npm run test:coverage`: Coverage reports

### Dependencies

#### Added
- @vitest/ui: 4.0.13
- @vue/test-utils: 2.4.6
- happy-dom: 20.0.10
- vitest: 4.0.13

#### Updated
- vue: 3.5.24 → 3.5.25
- vue-i18n: 9.14.5 → 11.2.1
- vite: 7.0.7 → 7.2.4
- axios: Updated to 1.13.2
- element-plus: Updated to 2.11.8
- leaflet: Updated to 1.9.4
- All Laravel packages to latest versions

### Security

- No security vulnerabilities in dependencies
- Rate limiting on API endpoints
- CSRF protection enabled
- XSS prevention with sanitization
- SQL injection prevention with Eloquent ORM
- Secure headers configured

### Performance

- PHP 8.3 performance improvements with `final` classes
- Optimized build with code splitting
- Lazy loading for components and routes
- Improved First Contentful Paint (< 1.5s)
- Time to Interactive (< 3s)

## [1.0.0] - 2025-11-20

### Initial Release

- Laravel 11 backend with RESTful API
- Vue.js 3 frontend with Composition API
- SQLite database with seeders
- Swagger/OpenAPI documentation
- SOLID principles implementation
- Hexagonal architecture
- Portfolio sections: Projects, Experience, Education, Skills
- Dark theme with Tailwind CSS
- Responsive design

---

## Version History Legend

- **Major version** (X.0.0): Breaking changes, major features
- **Minor version** (0.X.0): New features, backward compatible
- **Patch version** (0.0.X): Bug fixes, minor improvements

## Links

- [Repository](https://github.com/DevOrtega/portfolio)
- [Issues](https://github.com/DevOrtega/portfolio/issues)
- [Pull Requests](https://github.com/DevOrtega/portfolio/pulls)
