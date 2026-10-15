import type { Milestone } from '../../types'
import { RichText } from '../RichText'
import { Reveal, Section } from '../ui'

interface JourneyProps {
  milestones: Milestone[]
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
}

export function Journey({ milestones, tone, title, subtitle }: JourneyProps) {
  return (
    <Section id="journey" title={title || 'Journey'} subtitle={subtitle || 'Key moments along the way.'} tone={tone}>
      <div className="relative">
        {/* One continuous line behind every dot (desktop); mobile uses the list's own left border. */}
        <span aria-hidden="true" className="absolute bottom-2 left-[11rem] top-2 hidden w-px bg-navy/15 md:block" />
        <ol className="ml-2 border-l border-navy/15 md:ml-0 md:border-l-0">
          {milestones.map((m) => (
            <li key={m.id} className="relative pb-12 pl-8 last:pb-0 md:grid md:grid-cols-[11rem_1fr] md:pl-0">
              <span
                aria-hidden="true"
                className="absolute -left-[5px] top-3 h-2.5 w-2.5 rounded-full bg-gold ring-4 ring-gold/20 md:left-[11rem] md:-translate-x-1/2"
              />
              <p className="whitespace-nowrap font-serif text-lg font-semibold leading-8 text-gold-dark md:pr-10 md:text-right md:text-xl">
                {m.year}
              </p>
              <Reveal className="md:pl-10">
                <h3 className="text-xl font-semibold leading-8">{m.title}</h3>
                {m.description && <RichText html={m.description} className="mt-1 text-muted" />}
              </Reveal>
            </li>
          ))}
        </ol>
      </div>
    </Section>
  )
}
