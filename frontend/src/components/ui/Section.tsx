import type { ReactNode } from 'react'
import { cn } from '../../lib/cn'
import { Container } from './Container'
import { SectionTitle } from './SectionTitle'

type Tone = 'cream' | 'white' | 'navy'

interface SectionProps {
  id: string
  title?: string
  subtitle?: string
  /** Background; alternate cream and white down the page. */
  tone?: Tone
  children: ReactNode
}

const tones: Record<Tone, string> = {
  cream: 'bg-cream',
  white: 'bg-white',
  navy: 'bg-navy text-white',
}

export function Section({ id, title, subtitle, tone = 'cream', children }: SectionProps) {
  return (
    <section id={id} className={cn('py-16 md:py-24', tones[tone])}>
      <Container>
        {title && <SectionTitle title={title} subtitle={subtitle} light={tone === 'navy'} />}
        {children}
      </Container>
    </section>
  )
}
