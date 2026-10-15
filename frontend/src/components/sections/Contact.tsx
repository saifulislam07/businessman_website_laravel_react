import { Mail, MapPin, MessageCircle, Phone } from 'lucide-react'
import { lazy, Suspense, type ReactNode } from 'react'
import { whatsappUrl } from '../../lib/whatsapp'
import type { Settings } from '../../types'
import { Button, Card, Reveal, Section } from '../ui'

// The form (zod + react-hook-form) is below the fold, so it loads as its own chunk.
const ContactForm = lazy(() => import('./ContactForm').then((m) => ({ default: m.ContactForm })))

function InfoCard({ icon, title, children }: { icon: ReactNode; title: string; children: ReactNode }) {
  return (
    <Card className="flex gap-4 p-5">
      <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-navy text-gold">{icon}</span>
      <div className="min-w-0">
        <h3 className="font-sans text-base font-semibold text-navy">{title}</h3>
        <div className="mt-1 break-words text-sm text-muted">{children}</div>
      </div>
    </Card>
  )
}

interface ContactProps {
  settings: Settings
  tone: 'cream' | 'white'
  title?: string
  subtitle?: string
}

export function Contact({ settings, tone, title, subtitle }: ContactProps) {
  const name = settings.name ?? 'Your Name'
  const whatsapp = whatsappUrl(settings.whatsapp, name)

  return (
    <Section
      id="contact"
      title={title || 'Get in Touch'}
      subtitle={subtitle || 'Send a message and we will reply as soon as possible.'}
      tone={tone}
    >
      <div className="grid gap-10 lg:grid-cols-2">
        <Reveal>
          <Suspense fallback={<div className="min-h-[34rem] rounded-card border border-navy/10 bg-white" aria-hidden="true" />}>
            <ContactForm />
          </Suspense>
        </Reveal>

        <Reveal delay={0.1} className="space-y-4">
          {settings.brunei_address && (
            <InfoCard icon={<MapPin aria-hidden="true" className="h-5 w-5" />} title="Brunei Office">
              <address className="not-italic">{settings.brunei_address}</address>
            </InfoCard>
          )}
          {settings.malaysia_address && (
            <InfoCard icon={<MapPin aria-hidden="true" className="h-5 w-5" />} title="Malaysia Office">
              <address className="not-italic">{settings.malaysia_address}</address>
            </InfoCard>
          )}
          {settings.email && (
            <InfoCard icon={<Mail aria-hidden="true" className="h-5 w-5" />} title="Email">
              <a href={`mailto:${settings.email}`} className="underline underline-offset-4 hover:text-navy">
                {settings.email}
              </a>
            </InfoCard>
          )}
          {settings.phone && (
            <InfoCard icon={<Phone aria-hidden="true" className="h-5 w-5" />} title="Phone">
              <a href={`tel:${settings.phone.replace(/[^\d+]/g, '')}`} className="underline underline-offset-4 hover:text-navy">
                {settings.phone}
              </a>
            </InfoCard>
          )}

          {whatsapp && (
            <Button href={whatsapp} target="_blank" rel="noopener noreferrer" variant="primary" className="w-full">
              <MessageCircle aria-hidden="true" className="h-4 w-4" />
              Chat on WhatsApp
              <span className="sr-only"> (opens in a new tab)</span>
            </Button>
          )}
        </Reveal>
      </div>
    </Section>
  )
}
