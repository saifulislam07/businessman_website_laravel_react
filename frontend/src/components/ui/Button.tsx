import type { AnchorHTMLAttributes, ButtonHTMLAttributes } from 'react'
import { cn } from '../../lib/cn'

type Variant = 'primary' | 'outline' | 'outlineLight' | 'gold'

const variants: Record<Variant, string> = {
  primary: 'bg-navy text-white hover:bg-navy/90 border border-navy',
  outline: 'bg-transparent text-navy border border-navy hover:bg-navy hover:text-white',
  outlineLight: 'bg-transparent text-white border border-white/60 hover:bg-white hover:text-navy',
  gold: 'bg-gold text-navy border border-gold hover:bg-gold/90',
}

type ButtonProps = { variant?: Variant } & (
  | ({ href: string } & AnchorHTMLAttributes<HTMLAnchorElement>)
  | ({ href?: undefined } & ButtonHTMLAttributes<HTMLButtonElement>)
)

export function Button({ variant = 'primary', className, ...props }: ButtonProps) {
  const classes = cn(
    'inline-flex items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-medium transition-colors',
    'disabled:cursor-not-allowed disabled:opacity-60',
    variants[variant],
    className,
  )

  if (props.href !== undefined) {
    return <a className={classes} {...props} />
  }

  return <button type="button" className={classes} {...props} />
}
