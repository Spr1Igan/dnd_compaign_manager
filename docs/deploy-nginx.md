# Деплой на сервер с Nginx

Инструкция рассчитана на чистый Ubuntu-сервер, Nginx, PHP-FPM и GitHub-репозиторий проекта:

```text
https://github.com/Spr1Igan/dnd_compaign_manager.git
```

По умолчанию используется SQLite. Для текущего состояния приложения этого достаточно и проще всего для первого запуска.

## 1. Что нужно заранее

- Сервер с Ubuntu.
- Домен или поддомен, направленный на IP сервера.
- SSH-доступ к серверу.
- Пользователь с `sudo`.
- Открытые порты `80` и `443`.

В примерах ниже замени:

```text
example.com
/var/www/dnd_compaign_manager
```

на свой домен и путь проекта.

## 2. Установка пакетов

```bash
sudo apt update
sudo apt install -y nginx git unzip curl sqlite3
sudo apt install -y php8.3-fpm php8.3-cli php8.3-common php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-sqlite3 php8.3-bcmath php8.3-intl
sudo apt install -y composer nodejs npm
```

Проверь версии:

```bash
php -v
composer --version
node -v
npm -v
nginx -v
```

Проект требует PHP `8.3` или выше.

## 3. Клонирование проекта

```bash
cd /var/www
sudo git clone https://github.com/Spr1Igan/dnd_compaign_manager.git
sudo chown -R "$USER":www-data dnd_compaign_manager
cd /var/www/dnd_compaign_manager
```

## 4. Установка зависимостей

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Если `npm ci` ругается на lock-файл или версию Node, сначала проверь, что на сервере установлена актуальная Node.js, затем повтори команду.

## 5. Настройка `.env`

```bash
cp .env.example .env
nano .env
```

Минимальный production-вариант:

```dotenv
APP_NAME="D&D Campaign Manager"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://example.com

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=ru_RU

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public

MAIL_MAILER=log
VITE_APP_NAME="${APP_NAME}"
```

После сохранения сгенерируй ключ приложения:

```bash
php artisan key:generate
```

## 6. SQLite и права

```bash
touch database/database.sqlite
sudo chown -R "$USER":www-data storage bootstrap/cache database
sudo find storage bootstrap/cache database -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache database -type f -exec chmod 664 {} \;
```

Затем выполни миграции и сидеры:

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize
```

## 7. Настройка Nginx

Скопируй пример конфига:

```bash
sudo cp deploy/nginx/dnd-campaign-manager.conf /etc/nginx/sites-available/dnd-campaign-manager
sudo nano /etc/nginx/sites-available/dnd-campaign-manager
```

Внутри замени:

```text
server_name example.com;
root /var/www/dnd_compaign_manager/public;
fastcgi_pass unix:/run/php/php8.3-fpm.sock;
```

на свой домен, путь проекта и сокет PHP-FPM, если версия PHP отличается.

Включи сайт:

```bash
sudo ln -s /etc/nginx/sites-available/dnd-campaign-manager /etc/nginx/sites-enabled/dnd-campaign-manager
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl reload php8.3-fpm
```

## 8. Вариант для antiX/Debian без systemd: php-cgi

На antiX/runit PHP-FPM может не установиться из-за зависимостей `systemd`. В этом случае можно использовать `php-cgi` через `spawn-fcgi`.

Пакеты:

```bash
sudo apt install -y php-cli php-cgi php-common php-mbstring php-xml php-curl php-zip php-sqlite3 php-bcmath php-intl
sudo apt install -y composer spawn-fcgi
```

Установка init.d-скрипта из репозитория:

```bash
cd /var/www/dnd_compaign_manager
sudo cp deploy/init.d/php-cgi /etc/init.d/php-cgi
sudo chmod +x /etc/init.d/php-cgi
sudo update-rc.d php-cgi defaults
sudo service php-cgi start
sudo service php-cgi status
```

Проверка сокета:

```bash
ls -la /run/php/php-cgi.sock
```

В Nginx-конфиге для этого варианта используй:

```nginx
fastcgi_pass unix:/run/php/php-cgi.sock;
```

Если нужно перезапустить PHP-CGI:

```bash
sudo service php-cgi restart
sudo service nginx restart
```

## 9. HTTPS через Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d example.com
```

После выпуска сертификата проверь:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## 10. Обновление проекта с GitHub

Когда в GitHub появятся новые изменения:

```bash
cd /var/www/dnd_compaign_manager
git pull
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

## 11. Очереди

Сейчас приложение не завязано на фоновые задания для основных экранов персонажа. Но так как `QUEUE_CONNECTION=database`, для будущих задач можно добавить worker.

Минимальный ручной запуск:

```bash
php artisan queue:work --tries=3
```

Для production лучше оформить worker через `systemd`.

## 12. Быстрая диагностика

Проверка маршрутов:

```bash
php artisan route:list
```

Проверка миграций:

```bash
php artisan migrate:status
```

Логи Laravel:

```bash
tail -n 100 storage/logs/laravel.log
```

Логи Nginx:

```bash
sudo tail -n 100 /var/log/nginx/error.log
```

Частые причины ошибки `500`:

- не сгенерирован `APP_KEY`;
- нет прав на `storage` или `bootstrap/cache`;
- не создан `database/database.sqlite`;
- не выполнены миграции;
- Nginx смотрит не в папку `public`;
- PHP-FPM сокет в конфиге Nginx не совпадает с установленной версией PHP.
