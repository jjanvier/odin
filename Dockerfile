FROM php:8.3-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app/

# Install dependencies
RUN composer --ignore-platform-req=ext-gd -W update symfony/filesystem phpspec/phpspec
RUN composer install --no-interaction --no-progress

# Create rendered directory
RUN mkdir -p /app/rendered

# Set permissions
RUN chmod -R 777 /app/rendered

# Set environment variable for rendered directory
ENV ODIN_RENDER_DIR=/app/rendered

CMD ["php", "-a"]
