import { animate, useInView, useReducedMotion } from 'framer-motion'
import { useEffect, useRef, useState } from 'react'
import { DynamicIcon } from '../../lib/icons'
import type { About as AboutData, Stat } from '../../types'
import { ResponsiveImage } from '../ResponsiveImage'
import { RichText } from '../RichText'
import { Card, Reveal, Section } from '../ui'

type Tone = 'cream' | 'white'

/** Splits "15+" into a prefix, the number to animate and a suffix. */
function parseStat(value: string) {
  const match = value.match(/^(\D*)(\d+(?:\.\d+)?)(.*)$/)
  if (!match) return null
  return { prefix: match[1], target: Number(match[2]), suffix: match[3], decimals: match[2].split('.')[1]?.length ?? 0 }
}

function StatCard({ stat }: { stat: Stat }) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: '-40px' })
  const reduceMotion = useReducedMotion()
  const parsed = parseStat(stat.value)
  const target = parsed?.target
  const [current, setCurrent] = useState(0)

  useEffect(() => {
    if (!inView || target === undefined || reduceMotion) return
    const controls = animate(0, target, { duration: 1.2, ease: 'easeOut', onUpdate: setCurrent })
    return () => controls.stop()
  }, [inView, reduceMotion, target])

  const shown = reduceMotion ? parsed?.target ?? 0 : inView ? current : 0
  const display = parsed ? `${parsed.prefix}${shown.toFixed(parsed.decimals)}${parsed.suffix}` : stat.value

  return (
    <Card ref={ref} className="text-center">
      <DynamicIcon icon={stat.icon} aria-hidden="true" className="mx-auto h-6 w-6 text-gold-dark" />
      <p className="mt-3 font-serif text-4xl font-semibold text-navy">
        <span className="sr-only">{stat.value}</span>
        <span aria-hidden="true">{display}</span>
      </p>
      <p className="mt-1 text-sm text-muted">{stat.label}</p>
    </Card>
  )
}

interface AboutProps {
  about: AboutData | null
  stats: Stat[]
  name: string
  tone: Tone
  title?: string
  subtitle?: string
}

export function About({ about, stats, name, tone, title, subtitle }: AboutProps) {
  return (
    <Section id="about" title={title || 'About'} subtitle={subtitle || undefined} tone={tone}>
      {about && (
        <div className="grid items-start gap-12 lg:grid-cols-[1.4fr_1fr]">
          <Reveal className="text-lg leading-relaxed">
            {about.bio && <RichText html={about.bio} />}
            {about.vision && (
              <blockquote className="mt-8 border-l-2 border-gold pl-6 font-serif text-xl italic text-navy">
                {about.vision}
              </blockquote>
            )}
          </Reveal>
          {about.photo && (
            <Reveal delay={0.1}>
              <ResponsiveImage
                image={about.photo}
                alt={`${name} at work`}
                sizes="(min-width: 1024px) 28rem, 100vw"
                width={800}
                height={1000}
                className="aspect-[4/5] w-full rounded-card bg-navy/5 object-cover"
              />
            </Reveal>
          )}
        </div>
      )}

      {stats.length > 0 && (
        <div className={`grid grid-cols-2 gap-4 lg:grid-cols-4 ${about ? 'mt-16' : ''}`}>
          {stats.map((stat) => (
            <StatCard key={stat.id} stat={stat} />
          ))}
        </div>
      )}
    </Section>
  )
}
