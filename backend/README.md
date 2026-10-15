# Fayer Ahammed — Backend (Laravel 12)

JSON REST API (`/api/v1`) + Filament admin (`/admin`).

## Setup
1. `composer install`
2. `cp .env.example .env && php artisan key:generate`
3. Create a MySQL database and set `DB_*`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` in `.env`
4. `php artisan migrate:fresh --seed`
5. `php artisan storage:link && php artisan serve`

Seeding creates demo content, placeholder images (GD required) and the admin user.
