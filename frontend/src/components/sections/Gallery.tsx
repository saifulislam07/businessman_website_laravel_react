import { useMemo, useState } from 'react'
import { cn } from '../../lib/cn'
import type { GalleryItem } from '../../types'
import { Lightbox } from '../Lightbox'
import { ResponsiveImage } from '../ResponsiveImage'
import { Reveal, Section } from '../ui'

const ALL = 'All'

interface GalleryProps {
  items: GalleryItem[]
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
}

export function Gallery({ items, tone, title, subtitle }: GalleryProps) {
  const [category, setCategory] = useState(ALL)
  const [openIndex, setOpenIndex] = useState<number | null>(null)

  const visible = items.filter((item) => item.image)
  const categories = useMemo(
    () => [ALL, ...Array.from(new Set(visible.map((i) => i.category).filter((c): c is string => Boolean(c))))],
    [visible],
  )
  const filtered = category === ALL ? visible : visible.filter((i) => i.category === category)

  return (
    <Section
      id="gallery"
      title={title || 'Gallery'}
      subtitle={subtitle || 'Moments from business, events and community.'}
      tone={tone}
    >
      {categories.length > 2 && (
        <div className="mb-8 flex flex-wrap gap-2" role="group" aria-label="Filter by category">
          {categories.map((c) => (
            <button
              key={c}
              type="button"
              aria-pressed={category === c}
              onClick={() => setCategory(c)}
              className={cn(
                'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                category === c ? 'border-navy bg-navy text-white' : 'border-navy/20 text-navy hover:border-navy',
              )}
            >
              {c}
            </button>
          ))}
        </div>
      )}

      <ul className="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
        {filtered.map((item, i) => (
          <li key={item.id}>
            <Reveal>
              <button
                type="button"
                onClick={() => setOpenIndex(i)}
                aria-label={`View larger: ${item.caption ?? 'gallery image'}`}
                className="group block w-full overflow-hidden rounded-card border border-navy/10 bg-navy/5"
              >
                <ResponsiveImage
                  image={item.image!}
                  alt={item.caption ?? ''}
                  sizes="(min-width: 768px) 33vw, 50vw"
                  width={400}
                  height={300}
                  className="aspect-[4/3] w-full object-cover transition-transform duration-300 group-hover:scale-105 motion-reduce:transition-none"
                />
              </button>
            </Reveal>
          </li>
        ))}
      </ul>

      {openIndex !== null && (
        <Lightbox items={filtered} index={openIndex} onClose={() => setOpenIndex(null)} onIndexChange={setOpenIndex} />
      )}
    </Section>
  )
}
