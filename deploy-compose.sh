#!/bin/bash

# Portfolio deployment script for Docker Compose
# Usage: ./deploy-compose.sh [option]
# Options:
#   build   - Rebuild and restart
#   restart - Just restart
#   logs    - Show logs
#   stop    - Stop services
#   clean   - Remove everything (containers, volumes, images)

set -e

case "$1" in
  build)
    echo "🔨 Building and starting services..."
    docker-compose up -d --build
    echo "✅ Services started. Checking health..."
    sleep 5
    docker-compose ps
    ;;
    
  restart)
    echo "🔄 Restarting services..."
    docker-compose restart
    echo "✅ Services restarted"
    docker-compose ps
    ;;
    
  logs)
    echo "📋 Showing logs (Ctrl+C to exit)..."
    docker-compose logs -f
    ;;
    
  stop)
    echo "🛑 Stopping services..."
    docker-compose down
    echo "✅ Services stopped"
    ;;
    
  clean)
    echo "🧹 Cleaning up everything..."
    read -p "This will remove containers, volumes, and images. Continue? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
      docker-compose down -v --rmi all
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
    echo "  build   - Rebuild and restart containers"
    echo "  restart - Restart containers without rebuilding"
    echo "  logs    - Show container logs (real-time)"
    echo "  stop    - Stop all services"
    echo "  clean   - Remove everything (containers, volumes, images)"
    echo ""
    echo "Examples:"
    echo "  ./deploy-compose.sh build    # First deployment or after code changes"
    echo "  ./deploy-compose.sh restart  # Quick restart"
    echo "  ./deploy-compose.sh logs     # Monitor logs"
    ;;
esac
