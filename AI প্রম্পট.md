# AI কোডিং প্রম্পট

Claude Code, Cursor বা অন্য AI কোডিং টুলে এই প্রম্পটগুলো ক্রমানুসারে দিন — প্রতিটা শেষ হলে চালিয়ে টেস্ট করে তারপর পরেরটা। প্রম্পট ইংরেজিতে রাখা হয়েছে, কারণ কোডিং টুল ইংরেজিতে সবচেয়ে নির্ভুল কাজ করে। `[ ]` বন্ধনীর জায়গাগুলো নিজের তথ্য দিয়ে বদলে নিন।

## প্রম্পট ০: মাস্টার কনটেক্সট

এটা সবার আগে দিন। Claude Code ব্যবহার করলে এটা প্রজেক্ট রুটে `CLAUDE.md` হিসেবে (Cursor-এ `.cursorrules`) সেভ করে রাখুন — তাহলে প্রতিটা প্রম্পটে আর বলতে হবে না।

```
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

Reply "Ready" and wait for the first task.
```

## ব্যাকএন্ড (প্রম্পট ১–৩)

### প্রম্পট ১: Laravel সেটআপ, ডাটাবেস ও Seeder

```
Task 1 — Backend foundation in /backend.

1. Create a fresh Laravel 12 project. Configure MySQL in .env.example.
2. Install laravel/sanctum and spatie/laravel-medialibrary. Configure CORS to allow only
   FRONTEND_URL from .env (default http://localhost:5173).
3. Create migrations + Eloquent models for:
   - settings: key (unique), value (json)
   - abouts: bio (longtext), vision (text)  [photo via media library]
   - stats: label, value (string, e.g. "15+"), icon
   - ventures: name, slug (unique), industry, country (enum: BN, MY, OTHER), description,
     website_url (nullable), founded_year (nullable)  [logo via media library]
   - expertises: title, description, icon
   - milestones: year, title, description
   - gallery_items: caption, category  [image via media library]
   - partners: name, url  [logo via media library]
   - testimonials: name, designation, company, quote  [photo via media library]
   - contact_messages: name, email, phone, subject, message, ip, is_read (bool)
   All content tables (not contact_messages) also get sort_order (int, default 0) and
   is_active (bool, default true).
4. Add a reusable scope: active()->ordered() (is_active = true, order by sort_order).
5. Media conversions: "webp" (max 1600px) and "thumb" (400px) for every image collection.
6. Seeders with realistic demo data for a Brunei/Malaysia businessman (3 ventures,
   4 stats, 5 expertise items, 6 milestones, 4 partners, 3 testimonials, settings for name,
   tagline, email, phone, whatsapp, brunei_address, malaysia_address, social links,
   seo_title, seo_description). Use placeholder images.
7. Create an admin user seeder reading ADMIN_EMAIL and ADMIN_PASSWORD from .env.

Done when `php artisan migrate:fresh --seed` runs without errors.
```

### প্রম্পট ২: পাবলিক API ও কন্ট্যাক্ট ফর্ম

```
Task 2 — Public REST API in /backend.

1. routes/api.php, prefix /api/v1:
   - GET  /home      -> all sections in one response
   - GET  /settings
   - GET  /ventures
   - GET  /gallery?category=
   - POST /contact
2. Use API Resources for every model. Image fields return absolute URLs for the webp and
   thumb conversions. Response shape: { "data": ... }.
3. /home returns: settings (key/value object), about, stats, ventures, expertise,
   milestones, gallery, partners, testimonials — only active, ordered items.
   Cache it for 1 hour (Cache::remember, key "home").
4. Model observer on every content model: on saved/deleted -> forget the "home" cache.
5. POST /contact:
   - FormRequest validation: name required|max:100, email required|email,
     phone nullable|max:30, subject nullable|max:150, message required|max:2000,
     plus a honeypot field "website" that must be empty.
   - Verify a Cloudflare Turnstile token (TURNSTILE_SECRET in .env; skip when empty in local).
   - Rate limit: 3 requests per minute per IP.
   - Save to contact_messages with the IP.
   - Queue two mails: ContactReceived to ADMIN_NOTIFY_EMAIL, AutoReply to the sender
     (short, polite, signed "Office of Fayer Ahammed").
   - Return 201 with a success message; validation errors as standard 422 JSON.
6. Write Pest feature tests for /home and /contact (success, validation, honeypot, throttle).

Done when all tests pass and /api/v1/home returns the seeded data.
```

### প্রম্পট ৩: Filament অ্যাডমিন প্যানেল

```
Task 3 — Admin panel with Filament 3 in /backend.

1. Install Filament 3 at /admin. Only users in the users table can log in.
   Brand the panel: name "Fayer Ahammed — Admin", primary color #0F1E36.
2. A "Site Settings" custom page: one form for all settings keys (name, tagline, hero image,
   email, phone, WhatsApp, Brunei address, Malaysia address, map embed URL, LinkedIn,
   Facebook, Instagram, SEO title, SEO description, OG image). Saves to the settings table.
3. Resources (list + create/edit forms) for About, Stats, Ventures, Expertise, Milestones,
   Gallery, Partners, Testimonials:
   - SpatieMediaLibraryFileUpload for images (image editor, max 5MB)
   - is_active toggle column in the table, drag-and-drop reordering on sort_order
   - Ventures: slug auto-filled from name, country select (Brunei / Malaysia / Other)
4. ContactMessage resource: read-only view, is_read filter and toggle, mark-as-read on open,
   "Reply by email" action (mailto link), bulk delete, CSV export.
5. Dashboard widgets: unread messages count, total ventures, latest 5 messages.
6. Navigation groups: "Content", "Inbox", "Settings". Unread count badge on Messages.
7. Rate-limit the login (5 per minute).

Done when I can log in, edit every section, and see changes in /api/v1/home immediately.
```

## ফ্রন্টএন্ড (প্রম্পট ৪–৬)

### প্রম্পট ৪: React সেটআপ, ডিজাইন সিস্টেম ও API লেয়ার

```
Task 4 — Frontend foundation in /frontend.

1. Create a Vite + React 19 + TypeScript app. Install tailwindcss 4, framer-motion,
   @tanstack/react-query, axios, react-hook-form, zod, @hookform/resolvers,
   react-helmet-async, lucide-react (icons).
2. Tailwind theme from the design tokens: colors navy, gold, cream, ink, muted;
   fonts Playfair Display (headings) and Inter (body), self-hosted via @fontsource with
   font-display: swap.
3. src/api: axios client with baseURL from VITE_API_URL; typed functions getHome(),
   postContact(). src/types: TypeScript types matching the /api/v1/home response.
4. src/hooks/useHomeData.ts with TanStack Query (staleTime 5 min).
5. UI primitives in src/components/ui: Container, Section (id, title, subtitle, alternating
   background), SectionTitle (serif heading + small gold rule), Button (primary navy,
   outline, gold variants), Card, Reveal (fade-up on scroll with framer-motion, respects
   prefers-reduced-motion).
6. App.tsx renders a full-page loading skeleton while data loads and a friendly error
   state with a retry button.

Done when `npm run dev` shows the skeleton, then an empty layout fed by the real API.
```

### প্রম্পট ৫: সব সেকশন

```
Task 5 — Build every section in src/components/sections, using data from useHomeData.
Hide any section whose data is empty. Mobile-first; check at 360px, 768px, 1280px.

- Navbar: sticky, transparent over the hero then solid cream with a thin border on scroll.
  Name/logo left, links (About, Ventures, Expertise, Journey, Gallery, Contact) right, gold
  "Get in Touch" button. Active link follows scroll (useScrollSpy). Mobile: hamburger ->
  full-screen menu. Smooth scroll with offset for the navbar height.
- Hero: full viewport height on desktop. Left: small gold label "Brunei · Malaysia", serif
  name, tagline, two buttons (Get in Touch, View Ventures). Right: portrait photo in a
  rounded frame with a thin gold offset border. Navy background, light text.
- About: bio paragraphs + vision quote on the left, photo on the right; below, 4 stat
  cards with count-up animation when visible.
- Ventures: responsive grid (1/2/3 cols). Card: logo, name, industry, country flag badge
  (BN/MY), founded year, short description, "Visit website" link if present.
- Expertise: 3-column icon cards (lucide icon by name, fallback Briefcase).
- Journey: vertical timeline, year in gold on the left, title + description on the right;
  on mobile a single column with a left line.
- Gallery: category filter chips + masonry/grid of thumbs; click opens an accessible
  lightbox (Esc to close, arrow keys, focus trap). Lazy-load images.
- Partners & Testimonials: grayscale partner logo strip (color on hover) and a simple
  testimonial slider (auto-advance 6s, pause on hover, dots).
- Footer: name, short line, social icons, quick links, © year.

Use <img loading="lazy" srcSet> with the webp and thumb URLs and proper alt text.
```

### প্রম্পট ৬: কন্ট্যাক্ট সেকশন ও ফর্ম

```
Task 6 — Contact section.

1. Two columns: left = form, right = contact cards (Brunei office, Malaysia office, email,
   phone) + "Chat on WhatsApp" button (https://wa.me/<number>?text=...) + small Google Map
   iframe from settings (lazy-loaded).
2. Form with react-hook-form + zod: name, email, phone (optional), subject, message,
   hidden honeypot "website", Cloudflare Turnstile widget (VITE_TURNSTILE_SITE_KEY).
3. Submit with postContact via useMutation: disable the button and show a spinner while
   sending; show server 422 errors under each field; on success replace the form with a
   thank-you message; on 429 show "Too many attempts, please try again in a minute."
4. Small privacy note under the button (PDPA-friendly): data is used only to reply.
5. A floating WhatsApp button bottom-right on mobile.

Done when a real message reaches the database and both emails are queued.
```

## লঞ্চের প্রস্তুতি (প্রম্পট ৭–৯)

### প্রম্পট ৭: SEO ও পারফরম্যান্স

```
Task 7 — SEO and performance.

1. react-helmet-async: title, meta description, canonical, Open Graph and Twitter card
   tags from settings (fallback to sensible defaults in index.html).
2. JSON-LD: Person schema (name, jobTitle "Entrepreneur", address Brunei & Malaysia,
   sameAs social links, worksFor each venture) + Organization schema per venture.
3. Pre-render the home page at build time so crawlers get full HTML (use a Vite prerender
   plugin, or a small Puppeteer script that fetches the built page and writes dist/index.html).
   The build must read VITE_API_URL; fail loudly if the API is unreachable.
4. public/robots.txt and a sitemap.xml generated at build time. A 1200x630 og-image.
5. Performance: code-split Gallery and Testimonials with React.lazy, preload the hero image
   and the two fonts, add width/height to all images to avoid layout shift.
6. Run Lighthouse (mobile) and fix issues until Performance, Accessibility, Best Practices
   and SEO are all 90+. Report the final scores.
```

### প্রম্পট ৮: ডেপ্লয়মেন্ট ও CI

```
Task 8 — Production deployment.
Target: one Ubuntu 24.04 VPS in Singapore, Nginx, PHP 8.3-FPM, MySQL 8, Cloudflare in front.
- fayerahammed.com      -> /var/www/frontend/dist (static, SPA fallback to index.html)
- api.fayerahammed.com  -> /var/www/backend/public

1. Write the two Nginx server blocks (gzip, long cache for hashed assets, security headers:
   HSTS, X-Frame-Options, X-Content-Type-Options, a sensible CSP).
2. A deploy.sh for the backend: git pull, composer install --no-dev -o, migrate --force,
   config/route/view cache, queue:restart.
3. Supervisor config for the queue worker; cron entry for schedule:run.
4. spatie/laravel-backup: daily DB + uploads backup, keep 14 days, notify ADMIN_NOTIFY_EMAIL.
5. GitHub Actions: on push to main -> run backend tests and frontend build; if green, SSH
   deploy backend and upload frontend dist. Secrets via GitHub secrets only.
6. A production .env checklist (APP_ENV=production, APP_DEBUG=false, APP_URL,
   FRONTEND_URL, SANCTUM/SESSION domains, mail, Turnstile keys).

Give me step-by-step server setup commands I can copy, in order.
```

### প্রম্পট ৯: ফাইনাল রিভিউ

```
Task 9 — Pre-launch review. Act as a strict reviewer, not the author.

Check and report as a table (item, status pass/fail, fix):
- Every section on 360px, 768px, 1280px; no horizontal scroll, no overlapping text
- All links, nav scroll offsets, WhatsApp link, map, social icons
- Contact form: success, validation, honeypot, rate limit, both emails delivered
- Admin: edit each section -> change visible on site after cache clear
- Empty-section hiding works
- Accessibility: keyboard-only navigation, focus states, alt texts, contrast
- SEO: view-source shows real content, meta/OG tags, JSON-LD valid (Rich Results Test),
  sitemap and robots reachable
- Security: APP_DEBUG off, .env not public, CORS only for the site domain, headers present
- Console free of errors and warnings

Then fix every failed item and re-run the checks.
```

**টিপস:** প্রতিটা প্রম্পটের পর কোড রিভিউ করে Git commit করুন। কিছু ভুল হলে পুরো প্রম্পট আবার না দিয়ে নির্দিষ্ট সমস্যাটা বলুন (যেমন "Ventures card on 360px overflows — fix it")।
