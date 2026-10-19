import { lazy, Suspense } from 'react'
import type { Partner, Testimonial } from '../../types'
import { ResponsiveImage } from '../ResponsiveImage'
import { Reveal, Section } from '../ui'

// The slider is below the fold, so it ships in its own chunk.
const TestimonialSlider = lazy(() => import('../TestimonialSlider'))

function PartnerLogo({ partner }: { partner: Partner }) {
  return (
    <div className="flex h-24 w-48 shrink-0 items-center justify-center rounded-xl border border-navy/10 bg-white p-5">
      {partner.logo ? (
        <ResponsiveImage
          image={partner.logo}
          alt={partner.name}
          sizes="160px"
          width={160}
          height={64}
          className="h-12 w-36 object-contain"
        />
      ) : (
        <span className="text-center font-serif text-lg text-navy">{partner.name}</span>
      )}
    </div>
  )
}

// Right-to-left marquee: the set is rendered twice and the track slides by exactly one set.
function PartnerStrip({ partners }: { partners: Partner[] }) {
  const set = Array.from({ length: Math.ceil(8 / partners.length) }, () => partners).flat()

  return (
    <div className="overflow-hidden [mask-image:linear-gradient(to_right,transparent,#000_8%,#000_92%,transparent)]">
      <div className="flex w-max animate-marquee hover:[animation-play-state:paused] motion-reduce:animate-none">
        <ul aria-label="Partners" className="flex shrink-0 gap-6 pr-6">
          {set.map((partner, i) => (
            <li key={i}>
              <PartnerLogo partner={partner} />
            </li>
          ))}
        </ul>
        <ul aria-hidden="true" className="flex shrink-0 gap-6 pr-6 motion-reduce:hidden">
          {set.map((partner, i) => (
            <li key={i}>
              <PartnerLogo partner={partner} />
            </li>
          ))}
        </ul>
      </div>
    </div>
  )
}

interface Props {
  partners: Partner[]
  testimonials: Testimonial[]
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
  partnersLabel?: string
}

export function PartnersTestimonials({ partners, testimonials, tone, title, subtitle, partnersLabel }: Props) {
  const hasBoth = partners.length > 0 && testimonials.length > 0
  const heading = title || (testimonials.length > 0 ? 'Trusted by Partners' : 'Partners')

  return (
    <Section id="partners" title={heading} subtitle={subtitle || undefined} tone={tone}>
      {testimonials.length > 0 && (
        <Reveal>
          <Suspense fallback={<div className="mx-auto min-h-72 max-w-3xl" />}>
            <TestimonialSlider testimonials={testimonials} />
          </Suspense>
        </Reveal>
      )}
      {partners.length > 0 && (
        <Reveal className={hasBoth ? 'mt-20' : ''}>
          {hasBoth && <p className="mb-6 text-center text-xs font-medium uppercase tracking-[0.18em] text-muted">{partnersLabel || 'Our partners'}</p>}
          <PartnerStrip partners={partners} />
        </Reveal>
      )}
    </Section>
  )
}
