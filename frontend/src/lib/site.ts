/** Canonical origin of the public site, used for canonical, Open Graph and structured-data URLs. */
export const SITE_URL = ((import.meta.env.VITE_SITE_URL as string | undefined) ?? 'https://example.com').replace(/\/$/, '')

export const DEFAULT_TITLE = 'Your Name — Entrepreneur in Brunei & Malaysia'
export const DEFAULT_DESCRIPTION =
  'Official website of Your Name, entrepreneur and businessman with ventures across Brunei and Malaysia.'

/** Static 1200x630 fallback served from /public. */
export const DEFAULT_OG_IMAGE = `${SITE_URL}/og-image.png`
