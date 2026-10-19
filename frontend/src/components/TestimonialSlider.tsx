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

  const touchStart = useRef<number | null>(null)
  const go = (next: number) => setIndex((next + count) % count)

  const onTouchEnd = (x: number) => {
    if (touchStart.current === null) return
    const delta = x - touchStart.current
    touchStart.current = null
    if (Math.abs(delta) > 48) go(index + (delta < 0 ? 1 : -1))
  }

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
      onTouchStart={(e) => (touchStart.current = e.touches[0].clientX)}
      onTouchEnd={(e) => onTouchEnd(e.changedTouches[0].clientX)}
    >
      <div className="relative overflow-hidden rounded-xl bg-navy">
        <Quote aria-hidden="true" className="absolute right-6 top-6 h-20 w-20 text-gold/20 md:right-12 md:top-10 md:h-32 md:w-32" />
        {/* All slides sit side by side and stretch to the tallest one, so every slide has the same size. */}
        <div
          className="flex transition-transform duration-700 ease-in-out motion-reduce:transition-none"
          style={{ transform: `translateX(-${index * 100}%)` }}
          aria-live={paused ? 'polite' : 'off'}
        >
          {testimonials.map((t, i) => {
            const subtitle = [t.designation, t.company].filter(Boolean).join(', ')
            return (
              <figure
                key={t.id}
                aria-hidden={i !== index}
                inert={i !== index}
                className="relative flex w-full shrink-0 flex-col justify-between px-6 py-12 md:px-16 md:py-16"
              >
                <div>
                  <span aria-hidden="true" className="block h-px w-12 bg-gold" />
                  <blockquote className="mt-8 max-w-3xl font-serif text-2xl leading-relaxed text-white md:text-3xl">{t.quote}</blockquote>
                </div>
                <figcaption className="mt-10 flex items-center gap-4">
                  {t.photo && (
                    <ResponsiveImage
                      image={t.photo}
                      alt={`Photo of ${t.name}`}
                      sizes="56px"
                      width={56}
                      height={56}
                      className="h-14 w-14 rounded-full border border-white/20 object-cover"
                    />
                  )}
                  <div>
                    <p className="font-medium text-white">{t.name}</p>
                    {subtitle && <p className="text-sm text-white/70">{subtitle}</p>}
                  </div>
                </figcaption>
              </figure>
            )
          })}
        </div>

        {count > 1 && (
          <div className="relative flex gap-1 px-6 pb-8 md:px-16 md:pb-10">
            {testimonials.map((t, i) => (
              <button
                key={t.id}
                type="button"
                aria-label={`Show testimonial ${i + 1} of ${count}`}
                aria-current={i === index}
                onClick={() => go(i)}
                className="flex h-10 w-8 items-center justify-center"
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
