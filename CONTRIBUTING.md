# Contributing to Portfolio

Thank you for your interest in contributing to this project! This document provides guidelines and instructions for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing Guidelines](#testing-guidelines)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

### Our Standards

- Be respectful and inclusive
- Accept constructive criticism
- Focus on what's best for the community
- Show empathy towards others

## Getting Started

### Prerequisites

- PHP >= 8.3
- Node.js >= 24.x LTS
- npm >= 11.x
- Composer
- Git

### Setup Development Environment

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/portfolio.git
   cd portfolio
   ```

3. Install dependencies:
   ```bash
   composer install
   npm install
   ```

4. Setup environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

5. Run tests to verify setup:
   ```bash
   php artisan test
   npm test
   ```

## Development Workflow

### Branch Strategy

- `main`: Production-ready code
- `develop`: Integration branch (if needed)
- `feature/*`: New features
- `fix/*`: Bug fixes
- `docs/*`: Documentation updates
- `refactor/*`: Code refactoring
- `test/*`: Test additions or modifications

### Creating a Feature Branch

```bash
git checkout -b feature/your-feature-name
```

### Keeping Your Branch Updated

```bash
git fetch origin
git rebase origin/main
```

## Coding Standards

### PHP Standards

#### PSR-12 Compliance
We follow PSR-12 coding standards. Use Laravel Pint for formatting:

```bash
./vendor/bin/pint
```

#### Type Declarations
Always use type declarations:

```php
// Good
public function findById(int $id): ?Project
{
    return $this->repository->find($id);
}

// Bad
public function findById($id)
{
    return $this->repository->find($id);
}
```

#### Use Final Classes
Mark classes as `final` when they shouldn't be extended:

```php
final class ProjectService
{
    // ...
}
```

#### PHPDoc Blocks
Add comprehensive PHPDoc blocks:

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

### JavaScript/Vue Standards

#### Modern ES Features
Use modern JavaScript features:

```javascript
// Good
const value = maybeNull ?? defaultValue;
const property = object?.property;

// Bad
const value = maybeNull || defaultValue;
const property = object && object.property;
```

#### Vue 3 Composition API
Use Composition API with `<script setup>`:

```vue
<script setup>
import { ref, computed } from 'vue';

const count = ref(0);
const doubled = computed(() => count.value * 2);
</script>
```

#### Component Naming
Use PascalCase for components:

```javascript
// Good
import ProjectCard from '@/components/ProjectCard.vue';

// Bad
import projectCard from '@/components/project-card.vue';
```

### Code Organization

#### Backend Structure
```
app/
├── Domain/           # Business logic, entities
├── Application/      # Services, use cases
├── Infrastructure/   # DB, external services
└── Http/            # Controllers, requests
```

#### Frontend Structure
```
resources/js/
├── components/      # Reusable components
├── composables/     # Shared logic
├── views/          # Page components
└── router/         # Route configuration
```

## Testing Guidelines

### Test Requirements

- All new features must include tests
- Bug fixes must include regression tests
- Maintain or improve code coverage
- Tests must pass before submitting PR

### Backend Testing (PEST)

#### Unit Tests
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

#### Integration Tests
```php
it('returns projects via API', function () {
    ProjectModel::factory()->count(5)->create();
    
    $response = $this->getJson('/api/projects');
    
    $response->assertStatus(200)
        ->assertJsonCount(5);
});
```

### Frontend Testing (Vitest)

#### Component Tests
```javascript
it('renders project card correctly', () => {
  const wrapper = mount(ProjectCard, {
    props: { project: mockProject }
  });
  
  expect(wrapper.find('h3').text()).toBe(mockProject.title);
});
```

#### Composable Tests
```javascript
it('calculates responsive zoom', () => {
  const { getResponsiveZoom } = useBusMap();
  
  expect(getResponsiveZoom()).toBeGreaterThan(9);
});
```

### Running Tests

```bash
# Backend
php artisan test
php artisan test --filter=ProjectServiceTest

# Frontend
npm test
npm run test:ui
npm run test:coverage
```

## Commit Message Guidelines

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `perf`: Performance improvements

### Examples

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

### Commit Message Rules

- Use present tense ("add feature" not "added feature")
- Use imperative mood ("move cursor to..." not "moves cursor to...")
- First line should be 50 characters or less
- Reference issues and PRs in the footer

## 🔀 Pull Request Process

### Before Submitting

1. **Update your branch**:
   ```bash
   git fetch origin
   git rebase origin/main
   ```

2. **Run all tests**:
   ```bash
   php artisan test
   npm test
   ```

3. **Format your code**:
   ```bash
   ./vendor/bin/pint
   npm run lint # if available
   ```

4. **Update documentation** if needed

### PR Template

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

### Review Process

1. Submit your PR
2. Address review comments
3. Update PR based on feedback
4. Wait for approval from maintainers
5. PR will be merged once approved

## Areas for Contribution

### High Priority

- Performance optimizations
- Additional test coverage
- Documentation improvements
- Accessibility enhancements

### Good First Issues

- UI/UX improvements
- Translation updates
- Bug fixes
- Documentation typos

### Feature Requests

- Open an issue first to discuss
- Get approval before implementing
- Follow architecture guidelines
- Include comprehensive tests

## 📞 Getting Help

- **Issues**: [GitHub Issues](https://github.com/DevOrtega/portfolio/issues)
- **Discussions**: [GitHub Discussions](https://github.com/DevOrtega/portfolio/discussions)
- **Email**: contact@carlosmortega.dev

## Thank You

Your contributions help make this project better. We appreciate your time and effort!

---

<div align="center">
  <p><strong>Happy Contributing!</strong></p>
  <p>© 2025 Carlos Miguel Ortega Arencibia</p>
</div>
