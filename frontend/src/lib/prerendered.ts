import type { HomeData } from '../types'

/**
 * Home data embedded by the build-time prerender as <script id="home-data" type="application/json">.
 * A JSON data block is not executed, so it works under a strict Content-Security-Policy.
 * Returns undefined on the dev server, where there is no prerendered markup.
 */
export function readPrerenderedData(): HomeData | undefined {
  const el = document.getElementById('home-data')
  if (!el?.textContent) return undefined

  try {
    return JSON.parse(el.textContent) as HomeData
  } catch {
    return undefined
  }
}
