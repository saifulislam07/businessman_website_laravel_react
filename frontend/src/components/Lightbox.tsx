import { ChevronLeft, ChevronRight, X } from 'lucide-react'
import { useEffect, useRef } from 'react'
import type { GalleryItem } from '../types'

interface LightboxProps {
  items: GalleryItem[]
  index: number
  onClose: () => void
  onIndexChange: (index: number) => void
}

const FOCUSABLE = 'button:not([disabled])'

/** Accessible modal image viewer: Esc closes, arrow keys navigate, Tab stays inside. */
export function Lightbox({ items, index, onClose, onIndexChange }: LightboxProps) {
  const dialogRef = useRef<HTMLDivElement>(null)
  const closeRef = useRef<HTMLButtonElement>(null)
  const item = items[index]
  const hasMany = items.length > 1

  // Keep the latest handlers reachable from the single keydown listener.
  const latest = useRef({ index, count: items.length, onClose, onIndexChange })
  useEffect(() => {
    latest.current = { index, count: items.length, onClose, onIndexChange }
  })

  useEffect(() => {
    const previouslyFocused = document.activeElement as HTMLElement | null
    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    closeRef.current?.focus()

    const onKey = (e: KeyboardEvent) => {
      const { index: i, count, onClose: close, onIndexChange: change } = latest.current

      if (e.key === 'Escape') {
        close()
      } else if (e.key === 'ArrowRight' && count > 1) {
        change((i + 1) % count)
      } else if (e.key === 'ArrowLeft' && count > 1) {
        change((i - 1 + count) % count)
      } else if (e.key === 'Tab' && dialogRef.current) {
        const focusable = Array.from(dialogRef.current.querySelectorAll<HTMLElement>(FOCUSABLE))
        if (focusable.length === 0) return
        const first = focusable[0]
        const last = focusable[focusable.length - 1]
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault()
          last.focus()
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault()
          first.focus()
        }
      }
    }

    document.addEventListener('keydown', onKey)
    return () => {
      document.removeEventListener('keydown', onKey)
      document.body.style.overflow = previousOverflow
      previouslyFocused?.focus()
    }
  }, [])

  if (!item?.image) return null

  const iconButton =
    'absolute flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20'

  return (
    <div
      ref={dialogRef}
      role="dialog"
      aria-modal="true"
      aria-label={item.caption ?? 'Gallery image'}
      data-surface="dark"
      className="fixed inset-0 z-50 flex items-center justify-center bg-navy/95 p-4"
      onClick={(e) => e.target === e.currentTarget && onClose()}
    >
      <button ref={closeRef} type="button" aria-label="Close" onClick={onClose} className={`${iconButton} right-4 top-4`}>
        <X aria-hidden="true" />
      </button>

      {hasMany && (
        <button
          type="button"
          aria-label="Previous image"
          onClick={() => onIndexChange((index - 1 + items.length) % items.length)}
          className={`${iconButton} left-2 top-1/2 -translate-y-1/2 md:left-6`}
        >
          <ChevronLeft aria-hidden="true" />
        </button>
      )}

      <figure className="flex max-h-full max-w-5xl flex-col items-center">
        <img src={item.image.webp} alt={item.caption ?? ''} className="max-h-[80vh] w-auto max-w-full rounded-card object-contain" />
        {item.caption && <figcaption className="mt-4 text-center text-sm text-white/80">{item.caption}</figcaption>}
      </figure>

      {hasMany && (
        <button
          type="button"
          aria-label="Next image"
          onClick={() => onIndexChange((index + 1) % items.length)}
          className={`${iconButton} right-2 top-1/2 -translate-y-1/2 md:right-6`}
        >
          <ChevronRight aria-hidden="true" />
        </button>
      )}
    </div>
  )
}
