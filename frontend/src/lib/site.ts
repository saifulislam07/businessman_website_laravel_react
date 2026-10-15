/** Canonical origin of the public site, used for canonical, Open Graph and structured-data URLs. */
export const SITE_URL = ((import.meta.env.VITE_SITE_URL as string | undefined) ?? 'https://fayerahammed.com').replace(/\/$/, '')

export const DEFAULT_TITLE = 'Fayer Ahammed — Entrepreneur in Brunei & Malaysia'
export const DEFAULT_DESCRIPTION =
  'Official website of Fayer Ahammed, entrepreneur and businessman with ventures across Brunei and Malaysia.'

/** Static 1200x630 fallback served from /public. */
export const DEFAULT_OG_IMAGE = `${SITE_URL}/og-image.png`
