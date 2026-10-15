import { useInView, useReducedMotion } from 'framer-motion'
import { Quote } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { cn } from '../lib/cn'
import type { Testimonial } from '../types'
import { ResponsiveImage } from './ResponsiveImage'

const AUTO_ADVANCE_MS = 6000

export default function TestimonialSlider({ testimonials }: { testimonials: Testimonial[] }) {
  const [index, setIndex] = useState(0)
  const [paused, setPaused] = useState(false)
  const reduceMotion = useReducedMotion()
  const ref = useRef<HTMLDivElement>(null)
  // Only rotate while on screen: saves work and keeps the prerendered first slide stable.
  const visible = useInView(ref)
  const count = testimonials.length

  useEffect(() => {
    if (paused || !visible || reduceMotion || count < 2) return
    const timer = window.setInterval(() => setIndex((i) => (i + 1) % count), AUTO_ADVANCE_MS)
    return () => window.clearInterval(timer)
  }, [paused, visible, reduceMotion, count])

  const current = testimonials[Math.min(index, count - 1)]
  const subtitle = [current.designation, current.company].filter(Boolean).join(', ')

  return (
    <div
      ref={ref}
      role="group"
      aria-roledescription="carousel"
      aria-label="Testimonials"
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      onFocus={() => setPaused(true)}
      onBlur={() => setPaused(false)}
    >
      <div className="relative overflow-hidden rounded-xl bg-navy px-6 py-12 md:px-16 md:py-16">
        <Quote aria-hidden="true" className="absolute right-6 top-6 h-20 w-20 text-gold/20 md:right-12 md:top-10 md:h-32 md:w-32" />
        <figure key={current.id} className="relative animate-fade" aria-live={paused ? 'polite' : 'off'}>
          <span aria-hidden="true" className="block h-px w-12 bg-gold" />
          <blockquote className="mt-8 max-w-3xl font-serif text-2xl leading-relaxed text-white md:text-3xl">{current.quote}</blockquote>
          <figcaption className="mt-10 flex items-center gap-4">
            {current.photo && (
              <ResponsiveImage
                image={current.photo}
                alt={`Photo of ${current.name}`}
                sizes="56px"
                width={56}
                height={56}
                className="h-14 w-14 rounded-full border border-white/20 object-cover"
              />
            )}
            <div>
              <p className="font-medium text-white">{current.name}</p>
              {subtitle && <p className="text-sm text-white/70">{subtitle}</p>}
            </div>
          </figcaption>
        </figure>

        {count > 1 && (
          <div className="relative mt-10 flex gap-1">
            {testimonials.map((t, i) => (
              <button
                key={t.id}
                type="button"
                aria-label={`Show testimonial ${i + 1} of ${count}`}
                aria-current={i === index}
                onClick={() => setIndex(i)}
                className="flex h-6 w-6 items-center justify-center"
              >
                <span className={cn('h-1.5 rounded-full transition-all', i === index ? 'w-6 bg-gold' : 'w-1.5 bg-white/40')} />
              </button>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
