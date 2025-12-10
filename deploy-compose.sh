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
    
    echo "⏳ Waiting for services to initialize (Migrations running via AUTORUN)..."
    sleep 10
    
    echo "🌱 Seeding database (includes GTFS import)..."
    docker compose exec -T portfolio php artisan db:seed --force
    
    echo "🔥 Warming up Bus Cache..."
    docker compose exec -T portfolio php artisan bus:cache-warmup
    
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