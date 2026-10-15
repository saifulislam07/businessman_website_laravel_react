# Portfolio — Frontend (React + Vite + TypeScript)

Single-page portfolio site. Styling with Tailwind CSS 4; data from the Laravel API.

## Setup
1. `npm install`
2. `cp .env.example .env` and set `VITE_API_URL` (e.g. `http://localhost:8000/api/v1`)
3. Start the backend (`php artisan serve` in `/backend`)
4. `npm run dev` — opens on http://localhost:5173

## Scripts
- `npm run dev` — dev server
- `npm run build` — type-check, production build, then **prerender** (see below)
- `npm run og-image` — regenerate `public/og-image.png` (1200x630)
- `npm run lint` — lint

## Production build and prerender
`npm run build` runs `scripts/prerender.mjs` afterwards. It fetches `VITE_API_URL/home`, renders the
site in headless Chrome and writes the full HTML (with the data embedded as a JSON block) to
`dist/index.html`, plus `dist/sitemap.xml` and the sitemap line of `dist/robots.txt`.

- The **backend must be reachable** during the build; otherwise the build fails on purpose.
- Needs Chrome/Chromium: auto-detected, or set `CHROME_PATH`.
- Set `VITE_SITE_URL` (e.g. `https://example.com`) for canonical, Open Graph and sitemap URLs.
- Content changes in the admin appear after the next page load (the app refetches), but the
  prerendered HTML only updates on a new build. Rebuild after major content or SEO changes.
- If you add or remove a `React.lazy` boundary, update the `suspended` selectors in the prerender script.

## Structure
- `src/api` — axios client and typed API functions
- `src/types` — types matching `/api/v1/home`
- `src/hooks` — data hooks (TanStack Query)
- `src/components/ui` — design-system primitives
- `src/components/sections` — page sections (Task 5)
