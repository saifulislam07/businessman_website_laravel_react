import type { Settings } from '../../types'
import { ResponsiveImage } from '../ResponsiveImage'
import { Button, Container } from '../ui'

export function Hero({ settings, showVentures }: { settings: Settings; showVentures: boolean }) {
  const name = settings.name ?? 'Your Name'

  return (
    <section id="top" data-surface="dark" className="flex bg-navy pb-16 pt-28 text-white md:pb-24 lg:min-h-screen lg:items-center lg:pt-24">
      <Container className="grid items-center gap-12 lg:grid-cols-2">
        <div>
          <p className="text-sm font-medium uppercase tracking-[0.2em] text-gold">Brunei · Malaysia</p>
          <h1 className="mt-4 text-4xl font-semibold leading-tight text-white md:text-5xl lg:text-6xl">{name}</h1>
          {settings.tagline && <p className="mt-6 max-w-xl text-lg text-white/80">{settings.tagline}</p>}
          <div className="mt-10 flex flex-col gap-4 sm:flex-row">
            <Button href="#contact" variant="gold">
              Get in Touch
            </Button>
            {showVentures && (
              <Button href="#ventures" variant="outlineLight">
                View Ventures
              </Button>
            )}
          </div>
        </div>

        {settings.hero_image && (
          <div className="relative mx-auto w-full max-w-sm lg:ml-auto lg:mr-0 lg:max-w-md">
            <div aria-hidden="true" className="absolute inset-0 translate-x-4 translate-y-4 rounded-card border border-gold" />
            <ResponsiveImage
              image={settings.hero_image}
              alt={`Portrait of ${name}`}
              sizes="(min-width: 1024px) 28rem, 24rem"
              width={800}
              height={1000}
              loading="eager"
              fetchPriority="high"
              className="relative aspect-[4/5] w-full rounded-card bg-white/10 object-cover"
            />
          </div>
        )}
      </Container>
    </section>
  )
}
