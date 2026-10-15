import type { ImgHTMLAttributes } from 'react'
import type { ImageUrls } from '../types'

interface ResponsiveImageProps extends Omit<ImgHTMLAttributes<HTMLImageElement>, 'src' | 'srcSet' | 'alt'> {
  image: ImageUrls
  alt: string
}

/** Offers 400w, 800w and 1600w versions so each screen downloads only what it needs. */
export function ResponsiveImage({ image, alt, sizes = '100vw', loading = 'lazy', decoding = 'async', ...props }: ResponsiveImageProps) {
  return (
    <img
      src={image.webp}
      srcSet={`${image.thumb} 400w, ${image.medium} 800w, ${image.webp} 1600w`}
      sizes={sizes}
      alt={alt}
      loading={loading}
      decoding={decoding}
      {...props}
    />
  )
}
