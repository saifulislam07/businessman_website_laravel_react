// Generates public/og-image.png (1200x630). Run with: npm run og-image
import { mkdtempSync, writeFileSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { join } from 'node:path'
import { pathToFileURL } from 'node:url'
import { resolve } from 'node:path'
import puppeteer from 'puppeteer-core'
import { findChrome } from './chrome.mjs'

const font = (file) => pathToFileURL(resolve('node_modules/@fontsource/playfair-display/files', file)).href
const inter = pathToFileURL(resolve('node_modules/@fontsource-variable/inter/files/inter-latin-wght-normal.woff2')).href

const html = `<!doctype html><html><head><style>
@font-face{font-family:'Playfair';font-weight:700;src:url('${font('playfair-display-latin-700-normal.woff2')}')}
@font-face{font-family:'InterV';font-weight:100 900;src:url('${inter}')}
html,body{margin:0}
body{width:1200px;height:630px;background:#0F1E36;position:relative;overflow:hidden;font-family:'InterV',sans-serif}
.frame{position:absolute;inset:36px;border:1px solid rgba(201,162,75,.55)}
.wrap{position:absolute;left:96px;top:0;bottom:0;display:flex;flex-direction:column;justify-content:center}
.label{color:#C9A24B;letter-spacing:.28em;font-size:26px;font-weight:500;text-transform:uppercase}
h1{margin:28px 0 0;color:#fff;font:700 112px/1.05 'Playfair',serif}
.rule{width:96px;height:3px;background:#C9A24B;margin:36px 0}
p{margin:0;color:rgba(255,255,255,.82);font-size:34px;font-weight:400}
</style></head><body><div class="frame"></div><div class="wrap">
<div class="label">Brunei · Malaysia</div><h1>Your Name</h1><div class="rule"></div><p>Entrepreneur &amp; businessman</p>
</div></body></html>`

const browser = await puppeteer.launch({ executablePath: findChrome(), headless: true, args: ['--allow-file-access-from-files'] })
const page = await browser.newPage()
await page.setViewport({ width: 1200, height: 630 })
// Fonts are file:// URLs, which about:blank pages may not load, so open the HTML as a file too.
const file = join(mkdtempSync(join(tmpdir(), 'og-')), 'og.html')
writeFileSync(file, html)
await page.goto(pathToFileURL(file).href, { waitUntil: 'networkidle0' })
await page.evaluate(() => document.fonts.ready)
await page.screenshot({ path: 'public/og-image.png', type: 'png' })
await browser.close()
console.log('Wrote public/og-image.png')
