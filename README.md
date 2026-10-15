# Fayer Ahammed — Portfolio Website

A single-page, "quiet luxury" portfolio website for an entrepreneur based in Brunei and Malaysia.
Fast on mobile, SEO-friendly (prerendered HTML), and fully editable from an admin panel.

## Features
- One-page site with smooth-scroll navigation: Hero, About, Business Ventures, Expertise, Journey,
  Gallery (lightbox), Partners and Testimonials, Contact, Footer
- Sections with no content hide themselves automatically
- Admin panel for all content: text, images, section headings, order and visibility
- Contact form with WhatsApp button and office addresses
- WebP image conversions, responsive images, WCAG AA contrast, keyboard navigation
- Build-time prerendering, sitemap, Open Graph tags

## Tech stack
| Part | Stack |
| --- | --- |
| Backend (`/backend`) | Laravel 12, PHP 8.3, MySQL 8, Filament 3 admin, spatie/laravel-medialibrary |
| Frontend (`/frontend`) | React 19, Vite, TypeScript, Tailwind CSS 4, Framer Motion, TanStack Query, React Hook Form + Zod |

The backend is a JSON REST API under `/api/v1` plus the Filament admin at `/admin`.
The frontend is a separate React SPA that consumes the API.

## Getting started
1. **Backend** — see [backend/README.md](backend/README.md)
2. **Frontend** — see [frontend/README.md](frontend/README.md)

Quick version:

```bash
# backend
cd backend
composer install
cp .env.example .env && php artisan key:generate
# set DB_*, ADMIN_EMAIL, ADMIN_PASSWORD in .env
php artisan migrate:fresh --seed
php artisan storage:link && php artisan serve

# frontend (in another terminal)
cd frontend
npm install
cp .env.example .env   # set VITE_API_URL=http://localhost:8000/api/v1
npm run dev
```

Admin panel: http://localhost:8000/admin

## Deployment
Server setup, nginx, supervisor, cron and the deploy script live in [deploy/](deploy/).
Start with [deploy/SERVER-SETUP.md](deploy/SERVER-SETUP.md). Never commit `.env` files.

## Support
If this project helped you, you can buy me a coffee ☕

PayPal: **saiful2288@yahoo.com**

## License
All rights reserved unless a license file says otherwise. The demo content and placeholder images
in the seeders are for development only.
