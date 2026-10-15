import { cn } from '../lib/cn'
import type { ImageUrls } from '../types'

export interface LogoSet {
  dark: ImageUrls | null
  light: ImageUrls | null
}

interface LogoProps {
  name: string
  /** The logos uploaded in the admin; the bundled default is used for whichever is missing. */
  logos: LogoSet
  /** `light` is the white version, for dark backgrounds. */
  variant?: 'dark' | 'light'
  className?: string
}

// The default's intrinsic size (640 x 256) lets the browser reserve space before the image loads.
export function Logo({ name, logos, variant = 'dark', className }: LogoProps) {
  const uploaded = variant === 'light' ? logos.light : logos.dark
  const fallback = variant === 'light' ? '/logo-light.webp' : '/logo.webp'

  return (
    <img
      src={uploaded?.medium ?? fallback}
      alt={name}
      width={640}
      height={256}
      decoding="async"
      className={cn('h-12 w-auto', className)}
    />
  )
}
