// Runs after `vite build` (npm "postbuild"): renders the site in headless Chrome and writes
// the full HTML to dist/index.html so crawlers and first paint get real content.
// Also writes dist/sitemap.xml. Fails loudly (exit 1) if the API is unreachable.
import { readdirSync, readFileSync, writeFileSync } from 'node:fs'
import { resolve } from 'node:path'
import puppeteer from 'puppeteer-core'
import { loadEnv, preview } from 'vite'
import { findChrome } from './chrome.mjs'

const env = { ...loadEnv('production', process.cwd(), 'VITE_'), ...process.env }
const API_URL = (env.VITE_API_URL ?? '').replace(/\/$/, '')
const SITE_URL = (env.VITE_SITE_URL ?? 'https://fayerahammed.com').replace(/\/$/, '')
const DIST = resolve(env.PRERENDER_DIST ?? 'dist')
const PORT = 4173

async function fetchHome() {
  if (!API_URL) throw new Error('VITE_API_URL is not set.')
  let res
  try {
    res = await fetch(`${API_URL}/home`, { headers: { Accept: 'application/json' }, signal: AbortSignal.timeout(15000) })
  } catch (error) {
    throw new Error(`API unreachable at ${API_URL}/home (${error.message}). Start the backend before building.`)
  }
  if (!res.ok) throw new Error(`API returned HTTP ${res.status} for ${API_URL}/home.`)
  const body = await res.json()
  if (!body?.data?.settings) throw new Error('API response has no "data.settings"; refusing to prerender.')
  return body.data
}

const escapeJson = (value) =>
  JSON.stringify(value)
    .replace(/</g, '\\u003c')
    .replace(/\u2028/g, '\\u2028')
    .replace(/\u2029/g, '\\u2029')

const escapeAttr = (value) => String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;')

function fontPreloads() {
  const files = readdirSync(resolve(DIST, 'assets'))
  const wanted = [/^inter-latin-wght-normal-.*\.woff2$/, /^playfair-display-latin-600-normal-.*\.woff2$/]
  return wanted
    .map((pattern) => files.find((f) => pattern.test(f)))
    .filter(Boolean)
    .map((f) => `<link rel="preload" href="/assets/${f}" as="font" type="font/woff2" crossorigin>`)
}

async function prerender(data) {
  const server = await preview({ build: { outDir: DIST }, preview: { port: PORT, strictPort: true, host: '127.0.0.1' }, logLevel: 'error' })
  const browser = await puppeteer.launch({ executablePath: findChrome(), headless: true })

  try {
    const page = await browser.newPage()
    await page.setViewport({ width: 1280, height: 800 })

    // Serve the data fetched above so the page matches the embedded copy exactly (and needs no CORS).
    await page.setRequestInterception(true)
    page.on('request', (request) => {
      if (request.url().startsWith(`${API_URL}/home`)) {
        request.respond({
          status: 200,
          contentType: 'application/json',
          headers: { 'access-control-allow-origin': '*' },
          body: JSON.stringify({ data }),
        })
      } else {
        request.continue()
      }
    })

    const problems = []
    page.on('pageerror', (e) => problems.push(e.message))
    page.on('console', (m) => m.type() === 'error' && problems.push(m.text()))

    await page.goto(`http://127.0.0.1:${PORT}/`, { waitUntil: 'networkidle0', timeout: 60000 })
    await page.waitForSelector('#contact form', { timeout: 15000 })
    if (data.gallery.some((g) => g.image)) await page.waitForSelector('#gallery ul', { timeout: 15000 })
    if (data.testimonials.length) await page.waitForSelector('[aria-roledescription="carousel"]', { timeout: 15000 })

    // Helmet appends its tags after the defaults from index.html; keep only the last of each kind.
    const { html, hero } = await page.evaluate(() => {
      const keyOf = (el) => {
        if (el.tagName === 'TITLE') return 'title'
        if (el.tagName === 'META') return `meta:${el.getAttribute('name') ?? el.getAttribute('property')}`
        if (el.tagName === 'LINK' && el.getAttribute('rel') === 'canonical') return 'link:canonical'
        return null
      }
      const groups = new Map()
      document.head.querySelectorAll('title, meta, link[rel="canonical"]').forEach((el) => {
        const key = keyOf(el)
        if (key && !key.endsWith(':null')) groups.set(key, [...(groups.get(key) ?? []), el])
      })
      groups.forEach((els) => els.slice(0, -1).forEach((el) => el.remove()))

      // React.lazy content sits inside a <Suspense> boundary, which React's server renderer wraps in
      // "<!--$-->" ... "<!--/$-->". Keep this list in sync with the lazy() components in the app.
      const suspended = ['#gallery', '[aria-roledescription="carousel"]', '#contact form']
      suspended.forEach((selector) => {
        const el = document.querySelector(selector)
        if (!el) return
        el.before(document.createComment('$'))
        el.after(document.createComment('/$'))
      })

      // React renders adjacent text as separate nodes, but serializing HTML merges them. Re-insert
      // the "<!-- -->" separators that React's server renderer emits so hydration can match them.
      const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT)
      const texts = []
      for (let node = walker.nextNode(); node; node = walker.nextNode()) texts.push(node)
      texts.forEach((node) => {
        if (node.nextSibling?.nodeType === Node.TEXT_NODE) node.after(document.createComment(''))
      })
      const img = document.querySelector('img[fetchpriority="high"]')
      return {
        html: '<!doctype html>\n' + document.documentElement.outerHTML,
        hero: img && { src: img.currentSrc || img.src, srcset: img.getAttribute('srcset'), sizes: img.getAttribute('sizes') },
      }
    })

    if (!html.includes('<h1') || !html.includes(data.settings.name ?? '')) {
      throw new Error('Prerendered HTML is missing the main heading; the page did not render.')
    }
    if (problems.length) console.warn('Console messages during prerender:\n  ' + problems.join('\n  '))

    const preloads = fontPreloads()
    if (hero) {
      preloads.push(
        `<link rel="preload" as="image" href="${escapeAttr(hero.src)}"` +
          (hero.srcset ? ` imagesrcset="${escapeAttr(hero.srcset)}" imagesizes="${escapeAttr(hero.sizes ?? '100vw')}"` : '') +
          ' fetchpriority="high">',
      )
    }

    // Start the app after the first frame so the prerendered HTML paints before hydration
    // occupies the main thread. Script preloads are deliberately NOT emitted: they would compete
    // with the stylesheet, fonts and hero image for bandwidth (measured: Lighthouse mobile 89-92 -> 94-97).
    // The loader is a file (not inline) and the data a JSON block, so a strict CSP without
    // 'unsafe-inline' for scripts works.
    const entryTag = /<script type="module"[^>]*src="(\/assets\/index-([^"]+)\.js)"[^>]*><\/script>/
    const match = html.match(entryTag)
    if (!match) throw new Error('Could not find the entry <script type="module"> in the built HTML.')
    const [, entry, entryHash] = match
    const bootFile = `assets/boot-${entryHash}.js`
    writeFileSync(resolve(DIST, bootFile), `requestAnimationFrame(() => setTimeout(() => import('${entry}'), 0))\n`)

    // Vite adds runtime modulepreload links with the preview origin; make them site-relative.
    return html
      .replace(entryTag, `<script type="module" src="/${bootFile}"></script>`)
      .replaceAll(`http://127.0.0.1:${PORT}`, '')
      .replace(/<link rel="modulepreload"[^>]*>\s*/g, '')
      // Last in <head>, so <meta charset> stays within the first 1024 bytes.
      .replace('</head>', `${preloads.join('\n')}\n<script id="home-data" type="application/json">${escapeJson(data)}</script>\n</head>`)
  } finally {
    await browser.close()
    await new Promise((done) => server.httpServer.close(done))
  }
}

function writeSeoFiles() {
  const today = new Date().toISOString().slice(0, 10)
  writeFileSync(
    resolve(DIST, 'sitemap.xml'),
    `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n  <url>\n    <loc>${SITE_URL}/</loc>\n    <lastmod>${today}</lastmod>\n    <changefreq>monthly</changefreq>\n    <priority>1.0</priority>\n  </url>\n</urlset>\n`,
  )

  const robotsPath = resolve(DIST, 'robots.txt')
  const robots = readFileSync(robotsPath, 'utf8').replace(/^Sitemap:.*$/m, `Sitemap: ${SITE_URL}/sitemap.xml`)
  writeFileSync(robotsPath, robots)
}

try {
  const data = await fetchHome()
  const html = await prerender(data)
  writeFileSync(resolve(DIST, 'index.html'), html)
  writeSeoFiles()
  console.log(`Prerendered dist/index.html (${(html.length / 1024).toFixed(0)} kB), sitemap.xml and robots.txt for ${SITE_URL}`)
} catch (error) {
  console.error(`\nPrerender failed: ${error.message}\n`)
  process.exit(1)
}
