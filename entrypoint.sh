#!/bin/sh

# Đợi DB sẵn sàng (tùy chọn nhưng khuyến khích)
# sleep 5

# Tạo APP_KEY nếu chưa có
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
fi

# Tạo link storage nếu chưa có
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Chạy migrate nếu cần (cẩn thận với data cũ)
# php artisan migrate --force

# Dọn dẹp cache
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Cấp quyền cho storage và cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Thực thi lệnh chính (php-fpm)
exec "$@"
