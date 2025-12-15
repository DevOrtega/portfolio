# Contribuir al Portfolio

¡Gracias por tu interés en contribuir a este proyecto! Este documento proporciona pautas e instrucciones para contribuir.

## Índice

- [Código de Conducta](#código-de-conducta)
- [Primeros Pasos](#primeros-pasos)
- [Flujo de Trabajo](#flujo-de-trabajo)
- [Estándares de Código](#estándares-de-código)
- [Guías de Testing](#guías-de-testing)
- [Guías de Mensajes de Commit](#guías-de-mensajes-de-commit)
- [Proceso de Pull Request](#proceso-de-pull-request)

## 📜 Código de Conducta

### Nuestros Estándares

- Sé respetuoso e inclusivo
- Acepta críticas constructivas
- Enfócate en lo mejor para la comunidad
- Muestra empatía hacia los demás

## Primeros Pasos

### Prerrequisitos

- PHP >= 8.3
- Node.js >= 24.x LTS
- npm >= 11.x
- Composer
- Git

### Configurar Entorno de Desarrollo

1. Haz un fork del repositorio
2. Clona tu fork:
   ```bash
   git clone https://github.com/TU_USUARIO/portfolio.git
   cd portfolio
   ```

3. Instala dependencias:
   ```bash
   composer install
   npm install
   ```

4. Configura el entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

5. Ejecuta tests para verificar la instalación:
   ```bash
   php artisan test
   npm test
   ```

## Flujo de Trabajo

### Estrategia de Ramas

- `main`: Código listo para producción
- `develop`: Rama de integración (si es necesaria)
- `feature/*`: Nuevas características
- `fix/*`: Corrección de errores
- `docs/*`: Actualizaciones de documentación
- `refactor/*`: Refactorización de código
- `test/*`: Adición o modificación de tests

### Crear una Rama de Feature

```bash
git checkout -b feature/nombre-de-tu-feature
```

### Mantener tu Rama Actualizada

```bash
git fetch origin
git rebase origin/main
```

## Estándares de Código

### Estándares PHP

#### Cumplimiento PSR-12
Seguimos los estándares de codificación PSR-12. Usa Laravel Pint para formatear:

```bash
./vendor/bin/pint
```

#### Declaraciones de Tipos
Usa siempre declaraciones de tipos:

```php
// Bien
public function findById(int $id): ?Project
{
    return $this->repository->find($id);
}

// Mal
public function findById($id)
{
    return $this->repository->find($id);
}
```

#### Usar Clases Final
Marca las clases como `final` cuando no deban ser extendidas:

```php
final class ProjectService
{
    // ...
}
```

#### Bloques PHPDoc
Añade bloques PHPDoc completos:

```php
/**
 * Retrieve all projects from the repository
 *
 * @return Collection<int, Project>
 */
public function getAllProjects(): Collection
{
    return $this->repository->findAll();
}
```

### Estándares JavaScript/Vue

#### Características Modernas ES
Usa características modernas de JavaScript:

```javascript
// Bien
const value = maybeNull ?? defaultValue;
const property = object?.property;

// Mal
const value = maybeNull || defaultValue;
const property = object && object.property;
```

#### Vue 3 Composition API
Usa Composition API con `<script setup>`:

```vue
<script setup>
import { ref, computed } from 'vue';

const count = ref(0);
const doubled = computed(() => count.value * 2);
</script>
```

#### Nombramiento de Componentes
Usa PascalCase para componentes:

```javascript
// Bien
import ProjectCard from '@/components/ProjectCard.vue';

// Mal
import projectCard from '@/components/project-card.vue';
```

### Organización del Código

#### Estructura Backend
```
app/
├── Domain/           # Lógica de negocio, entidades
├── Application/      # Servicios, casos de uso
├── Infrastructure/   # BD, servicios externos
└── Http/            # Controladores, requests
```

#### Estructura Frontend
```
resources/js/
├── components/      # Componentes reutilizables
├── composables/     # Lógica compartida
├── views/          # Componentes de página
└── router/         # Configuración de rutas
```

## Guías de Testing

### Requisitos de Tests

- Todas las nuevas características deben incluir tests
- Las correcciones de bugs deben incluir tests de regresión
- Mantener o mejorar la cobertura de código
- Los tests deben pasar antes de enviar PR

### Testing Backend (PEST)

#### Tests Unitarios
```php
it('retrieves all projects', function () {
    $repository = Mockery::mock(ProjectRepositoryInterface::class);
    $repository->shouldReceive('findAll')
        ->once()
        ->andReturn(collect([/* ... */]));
    
    $service = new ProjectService($repository);
    $result = $service->getAllProjects();
    
    expect($result)->toHaveCount(3);
});
```

#### Tests de Integración
```php
it('returns projects via API', function () {
    ProjectModel::factory()->count(5)->create();
    
    $response = $this->getJson('/api/projects');
    
    $response->assertStatus(200)
        ->assertJsonCount(5);
});
```

### Testing Frontend (Vitest)

#### Tests de Componentes
```javascript
it('renders project card correctly', () => {
  const wrapper = mount(ProjectCard, {
    props: { project: mockProject }
  });
  
  expect(wrapper.find('h3').text()).toBe(mockProject.title);
});
```

#### Tests de Composables
```javascript
it('calculates responsive zoom', () => {
  const { getResponsiveZoom } = useBusMap();
  
  expect(getResponsiveZoom()).toBeGreaterThan(9);
});
```

### Ejecutar Tests

```bash
# Backend
php artisan test
php artisan test --filter=ProjectServiceTest

# Frontend
npm test
npm run test:ui
npm run test:coverage
```

## Guías de Mensajes de Commit

### Formato

```
<tipo>(<ámbito>): <asunto>

<cuerpo>

<pie>
```

### Tipos

- `feat`: Nueva característica
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Cambios de estilo de código (formateo, etc.)
- `refactor`: Refactorización de código
- `test`: Añadir o actualizar tests
- `chore`: Tareas de mantenimiento
- `perf`: Mejoras de rendimiento

### Ejemplos

```bash
# Feature
feat(api): add pagination to projects endpoint

# Bug fix
fix(auth): correct token expiration validation

# Documentation
docs(readme): update installation instructions

# Refactor
refactor(models): use final keyword for immutability

# Test
test(services): add unit tests for ProjectService
```

### Reglas de Mensajes de Commit

- Usa tiempo presente ("add feature" no "added feature")
- Usa modo imperativo ("move cursor to..." no "moves cursor to...")
- La primera línea debe tener 50 caracteres o menos
- Referencia issues y PRs en el pie

## 🔀 Proceso de Pull Request

### Antes de Enviar

1. **Actualiza tu rama**:
   ```bash
   git fetch origin
   git rebase origin/main
   ```

2. **Ejecuta todos los tests**:
   ```bash
   php artisan test
   npm test
   ```

3. **Formatea tu código**:
   ```bash
   ./vendor/bin/pint
   npm run lint # si está disponible
   ```

4. **Actualiza la documentación** si es necesario

### Plantilla de PR

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Backend tests pass
- [ ] Frontend tests pass
- [ ] Manual testing completed

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests added/updated
- [ ] All tests passing
```

### Proceso de Revisión

1. Envía tu PR
2. Atiende los comentarios de revisión
3. Actualiza el PR basado en el feedback
4. Espera la aprobación de los mantenedores
5. El PR será fusionado una vez aprobado

## Áreas para Contribuir

### Alta Prioridad

- Optimizaciones de rendimiento
- Cobertura de tests adicional
- Mejoras de documentación
- Mejoras de accesibilidad

### Buenos Primeros Issues

- Mejoras de UI/UX
- Actualizaciones de traducción
- Corrección de bugs
- Erratas en documentación

### Solicitudes de Características

- Abre un issue primero para discutir
- Obtén aprobación antes de implementar
- Sigue las guías de arquitectura
- Incluye tests completos

## 📞 Obtener Ayuda

- **Issues**: [GitHub Issues](https://github.com/DevOrtega/portfolio/issues)
- **Discussions**: [GitHub Discussions](https://github.com/DevOrtega/portfolio/discussions)
- **Email**: contact@carlosmortega.dev

## ¡Gracias!

Tus contribuciones ayudan a mejorar este proyecto. ¡Apreciamos tu tiempo y esfuerzo!

---

<div align="center">
  <p><strong>¡Feliz Contribución!</strong></p>
  <p>© 2025 Carlos Miguel Ortega Arencibia</p>
</div>
