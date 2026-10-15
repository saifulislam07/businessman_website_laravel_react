You are a senior full-stack developer. We are building a single-page portfolio website for
Fayer Ahammed, an entrepreneur/businessman based in Brunei and Malaysia. The site must look
simple, premium and professional ("quiet luxury"), load fast on mobile, and rank on Google
for his name.

ARCHITECTURE
- Monorepo: /backend (Laravel 12, PHP 8.3, MySQL 8) and /frontend (React 19 + Vite + TypeScript).
- Backend is a JSON REST API only, prefix /api/v1, served at api.fayerahammed.com.
- Admin panel: Filament 3 inside Laravel at /admin (no separate React admin).
- Frontend: React SPA at fayerahammed.com, Tailwind CSS 4, Framer Motion, TanStack Query,
  Axios, React Hook Form + Zod, react-helmet-async.
- Images: spatie/laravel-medialibrary with WebP conversions.

PAGE SECTIONS (one page, in this order, smooth-scroll nav with hash links)
Navbar, Hero, About (+ stats), Business Ventures, Expertise/Industries, Journey (milestone
timeline), Gallery (lightbox), Partners + Testimonials, Contact (form, Brunei & Malaysia office
addresses, WhatsApp button, map), Footer. A section with no data is hidden automatically.

DESIGN TOKENS
- Primary: deep navy #0F1E36. Accent: muted gold #C9A24B (use sparingly).
- Background: #FAF8F4 alternating with #FFFFFF. Text: #1F2937, secondary #6B7280.
- Headings: Playfair Display (serif). Body: Inter.
- Max container 1200px; section padding 96px (64px mobile); card radius 12px, thin borders,
  no heavy shadows; subtle fade-up on scroll (0.4s) only.
- Mobile-first, breakpoints 768 / 1024 / 1280. WCAG AA contrast, alt text, keyboard nav.

CODING RULES
- Clean, typed, readable code. Small components. No unused dependencies.
- Every content table has sort_order (int) and is_active (bool).
- Never commit .env. Write a short README for each app.
- After each task: list files changed, commands to run, and how to test it.
- Ask me before adding any library not listed above.
