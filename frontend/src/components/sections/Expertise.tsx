import { DynamicIcon } from '../../lib/icons'
import type { Expertise as ExpertiseItem } from '../../types'
import { ResponsiveImage } from '../ResponsiveImage'
import { RichText } from '../RichText'
import { Card, Reveal, Section } from '../ui'

interface ExpertiseProps {
  items: ExpertiseItem[]
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
}

export function Expertise({ items, tone, title, subtitle }: ExpertiseProps) {
  return (
    <Section
      id="expertise"
      title={title || 'Expertise'}
      subtitle={subtitle || 'Industries and disciplines where experience runs deepest.'}
      tone={tone}
    >
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {items.map((item, i) => (
          <Reveal key={item.id} delay={(i % 3) * 0.05} className="h-full">
            <Card className="h-full overflow-hidden p-0">
              {item.image && (
                <ResponsiveImage
                  image={item.image}
                  alt=""
                  sizes="(min-width: 1200px) 384px, (min-width: 768px) 50vw, 100vw"
                  width={800}
                  height={500}
                  className="aspect-[16/10] w-full object-cover"
                />
              )}
              <div className="p-6">
                <span className="flex h-12 w-12 items-center justify-center rounded-lg bg-navy text-gold">
                  <DynamicIcon icon={item.icon} aria-hidden="true" className="h-6 w-6" />
                </span>
                <h3 className="mt-5 text-xl font-semibold">{item.title}</h3>
                {item.description && <RichText html={item.description} className="mt-2 text-sm leading-relaxed text-muted" />}
              </div>
            </Card>
          </Reveal>
        ))}
      </div>
    </Section>
  )
}
