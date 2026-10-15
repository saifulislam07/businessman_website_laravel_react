# Personal Portfolio Website

A fast, single-page "quiet luxury" portfolio website for an entrepreneur or business owner, built with a
Laravel API, a Filament admin panel and a React front end. Every word, image and section heading is
editable from the admin panel, with no code changes needed.

- Mobile-first, prerendered HTML for fast loads and good Google ranking
- Content-managed: sections with no content hide themselves automatically
- Accessible: WCAG AA contrast, alt text, keyboard navigation, reduced-motion support

## Contents
- [Features](#features)
- [Tech stack](#tech-stack)
- [Project structure](#project-structure)
- [Getting started](#getting-started)
- [Configuration](#configuration)
- [Admin panel](#admin-panel)
- [API](#api)
- [Design system](#design-system)
- [Testing and quality](#testing-and-quality)
- [Deployment](#deployment)
- [Support](#support)

## Features

**Page sections** (one page, smooth-scroll navigation with hash links)

| Section | What it shows |
| --- | --- |
| Hero | Name, tagline, portrait, call-to-action buttons |
| About | Biography, vision quote, photo and key statistics |
| Business Ventures | Companies as a numbered list with logo, industry, year founded, country |
| Expertise | Industries and disciplines as icon cards |
| Journey | Milestone timeline; years can be a single year or a range (e.g. `2010 - 2011`) |
| Gallery | Filterable image grid with a lightbox |
| Partners and Testimonials | Testimonial slider and an auto-scrolling partner logo strip |
| Contact | Validated contact form, office addresses (two countries), email, phone, WhatsApp button |
| Footer | Navigation and social links |

**Platform**
- Admin panel for all content, ordering (drag and drop), visibility toggles and section headings
- Image uploads with automatic WebP conversions in three sizes
- Contact form with server-side validation, rate limiting, optional Cloudflare Turnstile and email notification
- SEO: editable page title and description, Open Graph image, canonical URL, sitemap, robots.txt
- Build-time prerendering of the full page HTML (headless Chrome)
- Floating WhatsApp button, lazy-loaded below-the-fold sections

## Tech stack

| Part | Stack |
| --- | --- |
| Backend | Laravel 12, PHP 8.3, MySQL 8, Filament 3, spatie/laravel-medialibrary |
| Frontend | React 19, Vite, TypeScript, Tailwind CSS 4, Framer Motion, TanStack Query, Axios, React Hook Form + Zod, react-helmet-async |
| Testing | Pest (backend), oxlint and `tsc` (frontend) |
| CI/CD | GitHub Actions: tests on pull requests, build and deploy on push to `main` |

## Project structure

```
.
├── backend/     Laravel 12 JSON API (/api/v1) and Filament admin (/admin)
│   ├── app/Filament        Admin resources and the Site Settings page
│   ├── app/Http            Controllers, API resources, requests
│   ├── app/Models          Eloquent models (every content table has sort_order and is_active)
│   ├── app/Services        Settings service
│   ├── database            Migrations and demo seeders
│   └── tests               Pest feature and unit tests
├── frontend/    React single-page app
│   ├── src/api             Axios client and typed API functions
│   ├── src/components      UI primitives and page sections
│   ├── src/hooks           Data hooks (TanStack Query)
│   ├── src/types           Types matching the API
│   └── scripts             Prerender and Open Graph image scripts
├── deploy/      Server setup guide, nginx, supervisor, cron and deploy scripts
└── .github/     CI and deploy workflow
```

## Getting started

### Requirements
- PHP 8.3 with the GD extension, Composer
- MySQL 8
- Node.js 20 or newer and npm
- Chrome or Chromium (only for the production build, which prerenders the page)

### 1. Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# edit .env: DB_*, ADMIN_EMAIL, ADMIN_PASSWORD, FRONTEND_URL
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```
Seeding creates demo content, placeholder images and the admin user. Replace the demo content from the
admin panel before going live.

### 2. Frontend
```bash
cd frontend
npm install
cp .env.example .env
# set VITE_API_URL=http://localhost:8000/api/v1
npm run dev
```
The site opens at http://localhost:5173 and the admin panel at http://localhost:8000/admin.

## Configuration

### Backend (`backend/.env`)
| Variable | Purpose |
| --- | --- |
| `APP_URL` | Public URL of the API |
| `DB_*` | MySQL connection |
| `FRONTEND_URL` | Allowed CORS origin (the React site) |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Admin user created by the seeder |
| `ADMIN_NOTIFY_EMAIL` | Where contact-form messages are emailed |
| `MAIL_*` | Mail server settings |
| `TURNSTILE_SECRET` | Optional Cloudflare Turnstile secret for the contact form |

### Frontend (`frontend/.env`)
| Variable | Purpose |
| --- | --- |
| `VITE_API_URL` | Base URL of the API, e.g. `https://api.example.com/api/v1` |
| `VITE_SITE_URL` | Public site URL, used for canonical, Open Graph and sitemap URLs |
| `VITE_TURNSTILE_SITE_KEY` | Optional Turnstile site key |

Never commit `.env` files. Only the `*.example` templates are tracked.

## Admin panel

Open `/admin` and sign in. Everything on the website is managed here:

| Menu | Manages |
| --- | --- |
| About, Stats | Biography, vision, photo and the statistic counters |
| Ventures | Companies: logo, name, industry, year, country, description |
| Expertise | Industry and discipline cards with icons |
| Milestones | Journey timeline entries (year or year range, title, description) |
| Gallery | Images with captions and categories |
| Partners, Testimonials | Partner logos and client quotes |
| Contact Messages | Messages sent through the contact form |
| Site Settings | Name, tagline, hero portrait, contact details, social links, **section headings and sub-headings**, and SEO |

Rows can be reordered by dragging and hidden with the "Show on the website" toggle. Changes appear on
the site straight away.

## API

JSON REST API, prefix `/api/v1`.

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/home` | All site content in one cached response |
| GET | `/settings` | Site settings |
| GET | `/ventures` | Business ventures |
| GET | `/gallery` | Gallery items |
| POST | `/contact` | Submit the contact form (rate limited) |

## Design system

| Token | Value |
| --- | --- |
| Primary | Deep navy `#0F1E36` |
| Accent | Muted gold `#C9A24B`, used sparingly |
| Background | `#FAF8F4`, alternating with `#FFFFFF` |
| Text | `#1F2937`, secondary `#6B7280` |
| Fonts | Playfair Display (headings), Inter (body) |
| Layout | 1200px container, 96px section padding (64px on mobile), 12px card radius |
| Breakpoints | 768, 1024, 1280 |

Motion is limited to a subtle 0.4s fade-up on scroll and respects `prefers-reduced-motion`.

## Testing and quality

```bash
# backend
cd backend && ./vendor/bin/pest

# frontend
cd frontend
npm run lint
npx tsc --noEmit -p tsconfig.app.json
```

## Deployment

The backend and frontend are deployed separately (API on a subdomain, static site on the main domain).
Server setup, nginx, supervisor queue worker, cron scheduler and the deploy script are in
[deploy/](deploy/). Start with [deploy/SERVER-SETUP.md](deploy/SERVER-SETUP.md).

Production frontend build:

```bash
cd frontend
npm run build
```

The build prerenders the page, so the API must be reachable. Rebuild after large content or SEO changes
to refresh the prerendered HTML.

## Support

If this project helped you, you can buy me a coffee ☕

**PayPal: saiful2288@yahoo.com**

## License

All rights reserved unless a license file says otherwise. Demo content and placeholder images in the
seeders are for development only.
