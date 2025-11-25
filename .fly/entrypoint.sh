#!/usr/bin/env bash

# Exit on error
set -e

echo "🚀 Starting Laravel application..."

# Run migrations
php artisan migrate --force --no-interaction

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if it doesn't exist
php artisan storage:link || true

# Start the application
echo "✅ Application ready!"
exec "$@"
