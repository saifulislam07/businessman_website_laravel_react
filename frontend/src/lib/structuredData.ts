import type { HomeData, Venture } from '../types'
import { DEFAULT_OG_IMAGE, SITE_URL } from './site'
import { stripHtml } from './text'

const PERSON_ID = `${SITE_URL}/#person`

const COUNTRY_CODE: Record<Venture['country'], string | undefined> = { BN: 'BN', MY: 'MY', OTHER: undefined }

const ventureId = (v: Venture) => `${SITE_URL}/#venture-${v.slug}`

/** schema.org graph: one Person who works for each venture, plus an Organization per venture. */
export function buildStructuredData(data: HomeData) {
  const { settings, ventures } = data
  const name = settings.name ?? 'Your Name'

  const sameAs = [settings.linkedin, settings.facebook, settings.instagram].filter(Boolean)

  const addresses = [
    settings.brunei_address && { '@type': 'PostalAddress', streetAddress: settings.brunei_address, addressCountry: 'BN' },
    settings.malaysia_address && { '@type': 'PostalAddress', streetAddress: settings.malaysia_address, addressCountry: 'MY' },
  ].filter(Boolean)

  const person = {
    '@type': 'Person',
    '@id': PERSON_ID,
    name,
    url: `${SITE_URL}/`,
    jobTitle: 'Entrepreneur',
    description: settings.seo_description,
    image: settings.og_image?.webp ?? settings.hero_image?.webp ?? DEFAULT_OG_IMAGE,
    email: settings.email ? `mailto:${settings.email}` : undefined,
    telephone: settings.phone,
    address: addresses.length ? addresses : undefined,
    sameAs: sameAs.length ? sameAs : undefined,
    worksFor: ventures.map((v) => ({ '@id': ventureId(v) })),
  }

  const organizations = ventures.map((v) => {
    const country = COUNTRY_CODE[v.country]
    return {
      '@type': 'Organization',
      '@id': ventureId(v),
      name: v.name,
      description: stripHtml(v.description),
      url: v.website_url ?? undefined,
      logo: v.logo?.webp,
      foundingDate: v.founded_year ? String(v.founded_year) : undefined,
      address: country ? { '@type': 'PostalAddress', addressCountry: country } : undefined,
      founder: { '@id': PERSON_ID },
    }
  })

  return { '@context': 'https://schema.org', '@graph': [person, ...organizations] }
}
