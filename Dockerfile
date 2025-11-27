FROM serversideup/php:8.3-fpm-nginx as base

# Switch to root to install packages
USER root

# Install Node.js and required PHP extensions
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs libicu-dev && \
    docker-php-ext-install bcmath intl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy package files
COPY package*.json ./

# Install Node dependencies
RUN npm ci

# Copy application files
COPY . .

# Setup environment and generate key as root
USER root

# Copy .env.example to .env if .env doesn't exist, then generate key
RUN if [ ! -f .env ]; then \
        cp .env.example .env; \
    fi && \
    php artisan key:generate --force && \
    touch database/database.sqlite && \
    chown www-data:www-data database/database.sqlite

# Run migrations and seeders
RUN php artisan migrate:fresh --seed --force

# Build frontend assets
RUN npm run build

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Cache configuration for production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Switch back to www-data user
USER www-data

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=30s \
  CMD curl -f http://localhost:8080/up || exit 1

# Start command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
