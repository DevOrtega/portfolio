# 🚀 Deployment a Fly.io

Este proyecto está configurado para desplegarse en Fly.io de forma sencilla.

## 📋 Pre-requisitos

1. **Instalar Fly CLI**

   ```bash
   curl -L https://fly.io/install.sh | sh
   ```
   
   Luego agrega al PATH (agregar al final de `~/.bashrc` o `~/.zshrc`):
   ```bash
   export FLYCTL_INSTALL="/home/YOUR_USERNAME/.fly"
   export PATH="$FLYCTL_INSTALL/bin:$PATH"
   ```
   
   Recarga la configuración:
   ```bash
   source ~/.bashrc  # o source ~/.zshrc
   ```

2. **Crear cuenta en Fly.io** (gratis, no requiere tarjeta)
   ```bash
   flyctl auth signup
   ```
   
   O si ya tienes cuenta:
   ```bash
   flyctl auth login
   ```

## 🎯 Primer Deployment

1. **Inicializar la app en Fly.io**
   ```bash
   flyctl launch
   ```
   
   Te preguntará:
   - ✅ App name: `portfolio-carlos` (o el que prefieras)
   - ✅ Region: `mad` (Madrid - más cercano a Canarias)
   - ✅ PostgreSQL: **NO** (usamos SQLite)
   - ✅ Redis: **NO** (no lo necesitamos aún)

2. **Configurar secretos/variables de entorno**
   ```bash
   # Generar APP_KEY
   php artisan key:generate --show
   
   # Setear el APP_KEY en Fly.io
   flyctl secrets set APP_KEY=tu_app_key_aqui
   ```

3. **Desplegar**
   ```bash
   flyctl deploy
   ```

## 🔄 Deployments Subsecuentes

Cada vez que quieras actualizar la app:

```bash
git add .
git commit -m "Actualización"
flyctl deploy
```

O usa el script automatizado:
```bash
./deploy.sh
```

## 🌐 Dominio Personalizado

### Usar dominio propio (ej: tuportfolio.com)

1. **Agregar dominio en Fly.io**
   ```bash
   flyctl certs create tuportfolio.com
   flyctl certs create www.tuportfolio.com
   ```

2. **Configurar DNS en tu proveedor**
   
   Fly.io te dará instrucciones específicas, pero generalmente:
   
   **Registros A:**
   ```
   @ (root)    A    [IP de Fly.io]
   www         CNAME portfolio-carlos.fly.dev
   ```

3. **Verificar certificado SSL**
   ```bash
   flyctl certs show tuportfolio.com
   ```

## 📊 Comandos Útiles

```bash
# Ver status de la app
flyctl status

# Ver logs en tiempo real
flyctl logs

# Abrir la app en el navegador
flyctl open

# Ver información de la app
flyctl info

# SSH a la máquina
flyctl ssh console

# Ejecutar comandos de Artisan
flyctl ssh console -C "php artisan migrate"

# Escalar recursos
flyctl scale vm shared-cpu-1x --memory 512

# Ver métricas
flyctl dashboard
```

## 🗄️ Base de Datos

### Opción 1: SQLite (actual, incluido en imagen)
- ✅ Gratis
- ✅ Sin configuración adicional
- ⚠️ Los datos se pierden al re-deploy

### Opción 2: PostgreSQL de Fly.io
```bash
# Crear base de datos
flyctl postgres create

# Conectarla a tu app
flyctl postgres attach [nombre-db]

# Actualizar .env
flyctl secrets set DB_CONNECTION=pgsql DB_DATABASE=postgres
```

### Opción 3: Supabase (PostgreSQL gratis externo)
```bash
flyctl secrets set \
  DB_CONNECTION=pgsql \
  DB_HOST=db.xxxx.supabase.co \
  DB_PORT=5432 \
  DB_DATABASE=postgres \
  DB_USERNAME=postgres \
  DB_PASSWORD=tu_password
```

## 🔧 Troubleshooting

### App no inicia
```bash
# Ver logs
flyctl logs

# SSH y verificar
flyctl ssh console
php artisan config:clear
```

### Falta memoria
```bash
# Aumentar RAM (de 256MB a 512MB)
flyctl scale memory 512
```

### Problemas con assets
```bash
# Rebuild completo
flyctl deploy --build-only
flyctl deploy
```

## 💰 Costos

- **Tier gratuito**: 3 VMs compartidas (suficiente para portfolio)
- **RAM**: 256MB gratis, 512MB = ~$2/mes
- **Almacenamiento**: 3GB gratis
- **Bandwidth**: 160GB/mes gratis

## 📱 Configuración Actual

- **App name**: `portfolio-carlos`
- **Region**: `mad` (Madrid)
- **RAM**: 256MB
- **DB**: SQLite (en imagen)
- **URL**: `https://portfolio-carlos.fly.dev`

## 🔐 Variables de Entorno

Para agregar variables:

```bash
# Una por una
flyctl secrets set VARIABLE_NAME=valor

# Múltiples a la vez
flyctl secrets set \
  APP_ENV=production \
  APP_DEBUG=false \
  APP_URL=https://tuportfolio.com
```

Para ver secretos actuales:
```bash
flyctl secrets list
```

## 📈 Monitoreo

```bash
# Métricas en tiempo real
flyctl dashboard

# Status de health checks
flyctl checks list

# Ver todas las regiones disponibles
flyctl platform regions
```

## 🚨 Rollback

Si algo sale mal:

```bash
# Ver historial de releases
flyctl releases

# Rollback a versión anterior
flyctl releases rollback [version-number]
```

## 📚 Recursos

- [Documentación oficial Fly.io](https://fly.io/docs/)
- [Laravel en Fly.io](https://fly.io/docs/laravel/)
- [Foro de comunidad](https://community.fly.io/)
