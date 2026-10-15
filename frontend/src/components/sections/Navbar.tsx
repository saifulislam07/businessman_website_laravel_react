import { Menu, X } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { useScrollSpy } from '../../hooks/useScrollSpy'
import { cn } from '../../lib/cn'
import { Button, Container } from '../ui'

export interface NavLink {
  id: string
  label: string
}

interface NavbarProps {
  name: string
  links: NavLink[]
}

export function Navbar({ name, links }: NavbarProps) {
  const [scrolled, setScrolled] = useState(false)
  const [open, setOpen] = useState(false)
  const firstMobileLink = useRef<HTMLAnchorElement>(null)
  const menuButton = useRef<HTMLButtonElement>(null)
  const active = useScrollSpy(links.map((l) => l.id))

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24)
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  // Mobile menu: lock page scroll, close on Escape, move focus in and back out.
  useEffect(() => {
    if (!open) return

    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    firstMobileLink.current?.focus()

    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setOpen(false)
    }
    document.addEventListener('keydown', onKey)

    const button = menuButton.current
    return () => {
      document.body.style.overflow = previousOverflow
      document.removeEventListener('keydown', onKey)
      button?.focus()
    }
  }, [open])

  const solid = scrolled || open

  return (
    <header
      data-surface={solid ? 'light' : 'dark'}
      className={cn(
        'fixed inset-x-0 top-0 z-40 transition-colors duration-300',
        solid ? 'border-b border-navy/10 bg-cream' : 'border-b border-transparent bg-transparent',
      )}
    >
      <Container className="flex h-[4.5rem] items-center justify-between">
        <a
          href="#top"
          className={cn('font-serif text-xl font-semibold transition-colors', solid ? 'text-navy' : 'text-white')}
        >
          {name}
        </a>

        <nav aria-label="Primary" className="hidden items-center gap-8 lg:flex">
          {links.map((link) => (
            <a
              key={link.id}
              href={`#${link.id}`}
              aria-current={active === link.id ? 'location' : undefined}
              className={cn(
                'border-b-2 py-1 text-sm font-medium transition-colors',
                active === link.id ? 'border-gold' : 'border-transparent',
                solid ? 'text-navy hover:text-navy/70' : 'text-white/90 hover:text-white',
              )}
            >
              {link.label}
            </a>
          ))}
          <Button href="#contact" variant="gold" className="px-5 py-2">
            Get in Touch
          </Button>
        </nav>

        <button
          ref={menuButton}
          type="button"
          className={cn('rounded-lg p-2 lg:hidden', solid ? 'text-navy' : 'text-white')}
          aria-label="Open menu"
          aria-expanded={open}
          aria-controls="mobile-menu"
          onClick={() => setOpen(true)}
        >
          <Menu aria-hidden="true" />
        </button>
      </Container>

      {open && (
        <div
          id="mobile-menu"
          role="dialog"
          aria-modal="true"
          aria-label="Menu"
          data-surface="dark"
          className="fixed inset-0 z-50 flex flex-col bg-navy px-4 py-4 text-white"
        >
          <div className="flex h-[3.5rem] items-center justify-between">
            <span className="font-serif text-xl font-semibold">{name}</span>
            <button type="button" className="rounded-lg p-2" aria-label="Close menu" onClick={() => setOpen(false)}>
              <X aria-hidden="true" />
            </button>
          </div>
          <nav aria-label="Mobile" className="mt-8 flex flex-1 flex-col gap-2">
            {links.map((link, i) => (
              <a
                key={link.id}
                ref={i === 0 ? firstMobileLink : undefined}
                href={`#${link.id}`}
                onClick={() => setOpen(false)}
                className="border-b border-white/10 py-4 font-serif text-2xl"
              >
                {link.label}
              </a>
            ))}
            <Button href="#contact" variant="gold" className="mt-8" onClick={() => setOpen(false)}>
              Get in Touch
            </Button>
          </nav>
        </div>
      )}
    </header>
  )
}
