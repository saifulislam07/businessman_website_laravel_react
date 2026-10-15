import { Helmet } from 'react-helmet-async'
import { DEFAULT_DESCRIPTION, DEFAULT_OG_IMAGE, DEFAULT_TITLE, SITE_URL } from '../lib/site'
import { buildStructuredData } from '../lib/structuredData'
import type { HomeData } from '../types'

/** Title, description, canonical, Open Graph, Twitter card and JSON-LD, driven by the admin settings. */
export function Seo({ data }: { data: HomeData }) {
  const { settings } = data
  const title = settings.seo_title || DEFAULT_TITLE
  const description = settings.seo_description || DEFAULT_DESCRIPTION
  const image = settings.og_image?.webp ?? DEFAULT_OG_IMAGE
  const canonical = `${SITE_URL}/`
  // "<" is escaped so the JSON can never close the script tag early.
  const jsonLd = JSON.stringify(buildStructuredData(data)).replace(/</g, '\\u003c')

  return (
    <Helmet>
      <title>{title}</title>
      <meta name="description" content={description} />
      <link rel="canonical" href={canonical} />

      <meta property="og:type" content="website" />
      <meta property="og:site_name" content={settings.name ?? 'Your Name'} />
      <meta property="og:title" content={title} />
      <meta property="og:description" content={description} />
      <meta property="og:url" content={canonical} />
      <meta property="og:image" content={image} />
      {!settings.og_image && <meta property="og:image:width" content="1200" />}
      {!settings.og_image && <meta property="og:image:height" content="630" />}
      <meta property="og:locale" content="en" />

      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content={title} />
      <meta name="twitter:description" content={description} />
      <meta name="twitter:image" content={image} />

      <script type="application/ld+json">{jsonLd}</script>
    </Helmet>
  )
}
