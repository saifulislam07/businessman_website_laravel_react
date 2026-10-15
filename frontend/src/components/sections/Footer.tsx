import type { ReactNode } from 'react'
import type { Settings } from '../../types'
import { FacebookIcon, InstagramIcon, LinkedinIcon } from '../BrandIcons'
import { Logo } from '../Logo'
import { Container } from '../ui'
import type { NavLink } from './Navbar'

const YEAR = new Date().getFullYear()

interface FooterProps {
  settings: Settings
  links: NavLink[]
}

export function Footer({ settings, links }: FooterProps) {
  const name = settings.name ?? 'Your Name'
  const socials: Array<{ label: string; href?: string; icon: ReactNode }> = [
    { label: 'LinkedIn', href: settings.linkedin, icon: <LinkedinIcon /> },
    { label: 'Facebook', href: settings.facebook, icon: <FacebookIcon /> },
    { label: 'Instagram', href: settings.instagram, icon: <InstagramIcon /> },
  ]

  return (
    <footer data-surface="dark" className="bg-navy py-12 text-white/80">
      <Container>
        <div className="grid gap-10 md:grid-cols-[1.5fr_1fr_auto]">
          <div>
            <Logo name={name} logos={{ dark: settings.logo, light: settings.logo_light }} variant="light" className="h-16" />
            <p className="mt-3 max-w-sm text-sm">Entrepreneur and businessman based in Brunei and Malaysia.</p>
          </div>

          <nav aria-label="Footer">
            <ul className="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
              {links.map((link) => (
                <li key={link.id}>
                  <a href={`#${link.id}`} className="hover:text-white">
                    {link.label}
                  </a>
                </li>
              ))}
            </ul>
          </nav>

          <ul className="flex gap-4">
            {socials
              .filter((s) => s.href)
              .map((s) => (
                <li key={s.label}>
                  <a
                    href={s.href}
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label={`${s.label} (opens in a new tab)`}
                    className="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 hover:border-gold hover:text-gold"
                  >
                    {s.icon}
                  </a>
                </li>
              ))}
          </ul>
        </div>

        <p className="mt-10 border-t border-white/10 pt-6 text-xs text-white/60">
          © {YEAR} {name}. All rights reserved.
        </p>
      </Container>
    </footer>
  )
}
