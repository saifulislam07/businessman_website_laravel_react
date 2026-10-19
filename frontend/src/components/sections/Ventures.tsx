import type { Country, Venture } from '../../types'
import { ResponsiveImage } from '../ResponsiveImage'
import { RichText } from '../RichText'
import { Reveal, Section } from '../ui'

const COUNTRY_LABEL: Record<Country, string> = { BN: 'Brunei', MY: 'Malaysia', OTHER: 'International' }

function initials(name: string): string {
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((word) => word[0])
    .join('')
    .toUpperCase()
}

interface VenturesProps {
  ventures: Venture[]
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
}

export function Ventures({ ventures, tone, title, subtitle }: VenturesProps) {
  return (
    <Section
      id="ventures"
      title={title || 'Business Ventures'}
      subtitle={subtitle || 'Companies built and led across Brunei and Malaysia.'}
      tone={tone}
    >
      <ol className="border-t border-navy/15">
        {ventures.map((venture, i) => (
          <Reveal key={venture.id}>
            <li className="grid gap-5 border-b border-navy/15 py-8 md:grid-cols-[4rem_5rem_1fr_auto] md:items-start md:gap-8 md:py-10">
              <span aria-hidden="true" className="hidden font-serif text-2xl text-gold-dark md:block">
                {String(i + 1).padStart(2, '0')}
              </span>

              {venture.logo ? (
                <ResponsiveImage
                  image={venture.logo}
                  alt={`${venture.name} logo`}
                  sizes="80px"
                  width={80}
                  height={80}
                  className="h-16 w-16 rounded-xl border border-navy/10 bg-white object-contain p-1.5 md:h-20 md:w-20"
                />
              ) : (
                <div
                  aria-hidden="true"
                  className="flex h-16 w-16 items-center justify-center rounded-xl border border-navy/10 bg-navy font-serif text-lg text-white md:h-20 md:w-20 md:text-xl"
                >
                  {initials(venture.name)}
                </div>
              )}

              <div>
                <h3 className="text-xl font-semibold md:text-2xl">{venture.name}</h3>
                <p className="mt-1 text-sm text-muted">
                  {venture.industry}
                  {venture.founded_year && ` · Est. ${venture.founded_year}`}
                </p>
                {venture.description && <RichText html={venture.description} className="mt-4 max-w-2xl text-sm leading-relaxed" />}
                {venture.cover && (
                  <ResponsiveImage
                    image={venture.cover}
                    alt=""
                    sizes="(min-width: 1200px) 640px, (min-width: 768px) 60vw, 100vw"
                    width={1200}
                    height={525}
                    className="mt-6 aspect-[16/7] w-full max-w-2xl rounded-card border border-navy/10 object-cover"
                  />
                )}
              </div>

              <p className="text-xs font-medium uppercase tracking-[0.18em] text-navy md:pt-2 md:text-right">
                {COUNTRY_LABEL[venture.country]}
              </p>
            </li>
          </Reveal>
        ))}
      </ol>
    </Section>
  )
}
