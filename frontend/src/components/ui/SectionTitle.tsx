import { cn } from '../../lib/cn'

interface SectionTitleProps {
  title: string
  subtitle?: string
  /** Use on navy backgrounds. */
  light?: boolean
  align?: 'left' | 'center'
}

export function SectionTitle({ title, subtitle, light = false, align = 'left' }: SectionTitleProps) {
  return (
    <div className={cn('mb-10 max-w-2xl md:mb-14', align === 'center' && 'mx-auto text-center')}>
      <h2 className={cn('text-3xl font-semibold md:text-4xl', light && 'text-white')}>{title}</h2>
      <span
        aria-hidden="true"
        className={cn('mt-4 block h-0.5 w-12 bg-gold', align === 'center' && 'mx-auto')}
      />
      {subtitle && <p className={cn('mt-4 text-base', light ? 'text-white/75' : 'text-muted')}>{subtitle}</p>}
    </div>
  )
}
