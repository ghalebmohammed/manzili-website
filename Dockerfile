FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy existing application directory contents
COPY . /app

# Install dependencies (Composer and NPM)
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Expose port 10000 for Render
EXPOSE 10000

# Ensure no broken host symlinks block the storage link
RUN rm -rf public/storage

# Start standard Laravel server by migrating, linking storage, and clearing cache first
CMD php artisan migrate --force && (php artisan storage:link || true) && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=10000
