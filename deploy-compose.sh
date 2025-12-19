#!/bin/bash

# Portfolio deployment script for Docker Compose
# Usage: ./deploy-compose.sh [option]
# Options:
#   build   - Rebuild, start, seed DB (GTFS) and warmup cache
#   restart - Just restart services
#   seed    - Run database seeders and cache warmup only
#   logs    - Show logs
#   stop    - Stop services
#   clean   - Remove everything (containers, volumes, images)

set -e

case "$1" in
  build)
    echo "🔨 Building and starting services..."
    docker compose build --no-cache
    docker compose up -d
    
    echo "⏳ Waiting for services to initialize..."
    sleep 5
    
    echo "🧹 Clearing old cache..."
    docker compose exec -T portfolio php artisan optimize:clear
    
    echo "📦 Running migrations..."
    docker compose exec -T portfolio php artisan migrate:fresh --force
    
    echo "🔄 Generating Global GTFS files..."
    docker compose exec -T portfolio php artisan bus:generate-global-gtfs
    
    echo "🌱 Seeding database (includes GTFS import)..."
    docker compose exec -T portfolio php artisan db:seed --force
    
    echo "🔥 Warming up Bus Cache..."
    docker compose exec -T portfolio php artisan bus:cache-warmup

    echo "⚡ Caching configuration..."
    docker compose exec -T portfolio php artisan config:cache
    docker compose exec -T portfolio php artisan route:cache
    docker compose exec -T portfolio php artisan view:cache
    
    echo "✅ Deployment complete. Checking status..."
    docker compose ps
    ;;
    
  restart)
    echo "🔄 Restarting services..."
    docker compose restart
    echo "✅ Services restarted"
    docker compose ps
    ;;

  seed)
    echo "🔄 Generating Global GTFS files..."
    docker compose exec -T portfolio php artisan bus:generate-global-gtfs

    echo "🌱 Seeding database (includes GTFS import)..."
    docker compose exec -T portfolio php artisan db:seed --force
    
    echo "🔥 Warming up Bus Cache..."
    docker compose exec -T portfolio php artisan bus:cache-warmup
    echo "✅ Data refreshed"
    ;;
    
  logs)
    echo "📋 Showing logs (Ctrl+C to exit)..."
    docker compose logs -f
    ;;
    
  stop)
    echo "🛑 Stopping services..."
    docker compose down
    echo "✅ Services stopped"
    ;;
    
  clean)
    echo "🧹 Cleaning up everything..."
    read -p "This will remove containers, volumes, and images. Continue? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
      docker compose down -v --rmi all
      echo "✅ Cleanup complete"
    else
      echo "❌ Cleanup cancelled"
    fi
    ;;
    
  deploy)
    echo "🚀 Starting PRODUCTION deployment..."
    
    # 1. GENERATE OSRM DATA (CRITICAL FOR HIKING DEMO)
    echo "Generating OSRM data (gran-canaria.osrm)..."
    ./init-osrm.sh # Assuming init-osrm.sh is in the root and executable
    
    echo "⬇️ Pulling latest images..."
    docker compose pull
    
    echo "🔨 Rebuilding containers (if needed)..."
    docker compose up -d --build
    
    echo "⏳ Waiting for services (15s)..."
    sleep 15
    
    echo "📦 Running migrations..."
    docker compose exec -T portfolio php artisan migrate --force
    
    # 2. SEED DATABASE (CRITICAL FOR HIKING PLANNER TO SHOW UP)
    echo "🌱 Seeding database (includes demo data)..."
    docker compose exec -T portfolio php artisan db:seed --force
    
    echo "🔥 Warming up Bus Cache..."
    docker compose exec -T portfolio php artisan bus:cache-warmup
    
    echo "🧹 Clearing old cache..."
    docker compose exec -T portfolio php artisan optimize:clear

    echo "🔥 Warming up caches..."
    docker compose exec -T portfolio php artisan config:cache
    docker compose exec -T portfolio php artisan route:cache
    docker compose exec -T portfolio php artisan view:cache
    
    echo "✅ Production deployment successful!"
    docker compose ps
    ;;
  *)
    echo "Portfolio Deployment Script"
    echo ""
    echo "Usage: ./deploy-compose.sh [option]"
    echo ""
    echo "Options:"
    echo "  build   - Rebuild, start, seed DB (GTFS) and warmup cache"
    echo "  restart - Restart containers without rebuilding"
    echo "  seed    - Run database seeders and cache warmup only"
    echo "  logs    - Show container logs (real-time)"
    echo "  stop    - Stop all services"
    echo "  clean   - Remove everything (containers, volumes, images)"
    echo ""
    echo "Examples:"
    echo "  ./deploy-compose.sh build    # Full deployment (recommended)"
    echo "  ./deploy-compose.sh seed     # Refresh data only"
    echo "  ./deploy-compose.sh logs     # Monitor logs"
    ;;
esac