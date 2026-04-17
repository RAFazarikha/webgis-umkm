#!/bin/bash

# ==============================
# CONFIG
# ==============================
PROJECT_PATH="/var/www/webgis-umkm"
BRANCH="main"
PHP_FPM="php8.4-fpm"
WEB_SERVER="nginx"

# ==============================
# MASUK KE PROJECT
# ==============================
echo "Masuk ke folder project..."
cd $PROJECT_PATH || exit

# ==============================
# FIX OWNERSHIP & PERMISSION
# ==============================
echo "Set permission..."
sudo chown -R ubuntu:www-data $PROJECT_PATH
sudo chmod -R 775 $PROJECT_PATH

# ==============================
# FIX GIT SAFE DIRECTORY
# ==============================
echo "Set safe directory..."
git config --global --add safe.directory $PROJECT_PATH

# ==============================
# PULL CODE TERBARU
# ==============================
echo "Pull repository..."
git pull origin $BRANCH

# ==============================
# INSTALL DEPENDENCY
# ==============================
echo "Install dependency..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# ==============================
# CLEAR CACHE (PENTING)
# ==============================
echo "Clear cache..."
php artisan optimize:clear

# ==============================
# REBUILD CACHE
# ==============================
echo "Rebuild cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ==============================
# MIGRATION (OPSIONAL)
# ==============================
echo "Run migration..."
php artisan migrate --force

# ==============================
# STORAGE LINK
# ==============================
echo "Storage link..."
php artisan storage:link

# ==============================
# RESTART SERVICE
# ==============================
echo "Restart service..."
sudo systemctl restart $PHP_FPM
sudo systemctl restart $WEB_SERVER

# ==============================
# DONE
# ==============================
echo "Deploy selesai 🚀"
