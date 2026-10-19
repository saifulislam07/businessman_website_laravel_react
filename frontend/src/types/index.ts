/** Types matching the Laravel API (GET /api/v1/home and POST /api/v1/contact). */

export interface ImageUrls {
  /** Up to 1600px wide. */
  webp: string
  /** Up to 800px wide. */
  medium: string
  /** Up to 400px wide. */
  thumb: string
}

export type Country = 'BN' | 'MY' | 'OTHER'

export interface Settings {
  name?: string
  tagline?: string
  email?: string
  phone?: string
  whatsapp?: string
  brunei_address?: string
  malaysia_address?: string
  map_embed_url?: string
  linkedin?: string
  facebook?: string
  instagram?: string
  seo_title?: string
  seo_description?: string
  about_title?: string
  about_subtitle?: string
  ventures_title?: string
  ventures_subtitle?: string
  expertise_title?: string
  expertise_subtitle?: string
  journey_title?: string
  journey_subtitle?: string
  gallery_title?: string
  gallery_subtitle?: string
  partners_title?: string
  partners_subtitle?: string
  partners_label?: string
  contact_title?: string
  contact_subtitle?: string
  hero_image: ImageUrls | null
  og_image: ImageUrls | null
  logo: ImageUrls | null
  logo_light: ImageUrls | null
}

export interface About {
  id: number
  bio: string
  vision: string | null
  photo: ImageUrls | null
}

export interface Stat {
  id: number
  label: string
  value: string
  icon: string | null
}

export interface Venture {
  id: number
  name: string
  slug: string
  industry: string
  country: Country
  description: string | null
  website_url: string | null
  founded_year: number | null
  logo: ImageUrls | null
  cover: ImageUrls | null
}

export interface Expertise {
  id: number
  title: string
  description: string | null
  icon: string | null
  image: ImageUrls | null
}

export interface VisitedCountry {
  id: number
  code: string
  name: string
}

export interface Milestone {
  id: number
  year: string
  title: string
  description: string | null
  image: ImageUrls | null
}

export interface GalleryItem {
  id: number
  caption: string | null
  category: string | null
  image: ImageUrls | null
}

export interface Partner {
  id: number
  name: string
  url: string | null
  logo: ImageUrls | null
}

export interface Testimonial {
  id: number
  name: string
  designation: string | null
  company: string | null
  quote: string
  photo: ImageUrls | null
}

export interface HomeData {
  settings: Settings
  about: About | null
  stats: Stat[]
  countries: VisitedCountry[]
  ventures: Venture[]
  expertise: Expertise[]
  milestones: Milestone[]
  gallery: GalleryItem[]
  partners: Partner[]
  testimonials: Testimonial[]
}

export interface ContactPayload {
  name: string
  email: string
  phone?: string
  subject?: string
  message: string
  /** Honeypot: must stay empty. */
  website?: string
  turnstile_token?: string
}

export interface ContactResponse {
  message: string
}
