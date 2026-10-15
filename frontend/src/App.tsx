import { lazy, Suspense, useEffect, type ReactNode } from 'react'
import { ErrorState } from './components/ErrorState'
import { LoadingSkeleton } from './components/LoadingSkeleton'
import { FloatingWhatsApp } from './components/FloatingWhatsApp'
import { About } from './components/sections/About'
import { Contact } from './components/sections/Contact'
import { Expertise } from './components/sections/Expertise'
import { Footer } from './components/sections/Footer'
import { Hero } from './components/sections/Hero'
import { Journey } from './components/sections/Journey'
import { Navbar } from './components/sections/Navbar'
import { Seo } from './components/Seo'
import { PartnersTestimonials } from './components/sections/PartnersTestimonials'
import { Ventures } from './components/sections/Ventures'
import { useHomeData } from './hooks/useHomeData'
import { whatsappUrl } from './lib/whatsapp'

// The gallery and lightbox are below the fold, so they load as a separate chunk.
const Gallery = lazy(() => import('./components/sections/Gallery').then((m) => ({ default: m.Gallery })))

type Tone = 'cream' | 'white'

interface SectionDef {
  id: string
  label: string
  /** A section with no data is not rendered at all. */
  visible: boolean
  render: (tone: Tone) => ReactNode
}

export default function App() {
  const { data, isPending, isError, refetch, isFetching } = useHomeData()

  // Sections render after the data arrives, so honour a #hash in the URL once they exist.
  useEffect(() => {
    if (isPending || !window.location.hash) return
    document.getElementById(window.location.hash.slice(1))?.scrollIntoView()
  }, [isPending])

  if (isPending) return <LoadingSkeleton />
  if (isError) return <ErrorState onRetry={() => void refetch()} retrying={isFetching} />

  const s = data.settings
  const name = s.name ?? 'Fayer Ahammed'

  const sections: SectionDef[] = [
    {
      id: 'about',
      label: 'About',
      visible: data.about !== null || data.stats.length > 0,
      render: (tone) => <About about={data.about} stats={data.stats} name={name} tone={tone} title={s.about_title} subtitle={s.about_subtitle} />,
    },
    {
      id: 'ventures',
      label: 'Ventures',
      visible: data.ventures.length > 0,
      render: (tone) => <Ventures ventures={data.ventures} tone={tone} title={s.ventures_title} subtitle={s.ventures_subtitle} />,
    },
    {
      id: 'expertise',
      label: 'Expertise',
      visible: data.expertise.length > 0,
      render: (tone) => <Expertise items={data.expertise} tone={tone} title={s.expertise_title} subtitle={s.expertise_subtitle} />,
    },
    {
      id: 'journey',
      label: 'Journey',
      visible: data.milestones.length > 0,
      render: (tone) => <Journey milestones={data.milestones} tone={tone} title={s.journey_title} subtitle={s.journey_subtitle} />,
    },
    {
      id: 'gallery',
      label: 'Gallery',
      visible: data.gallery.some((item) => item.image),
      render: (tone) => (
        <Suspense fallback={<section id="gallery" className="min-h-[40rem]" aria-hidden="true" />}>
          <Gallery items={data.gallery} tone={tone} title={s.gallery_title} subtitle={s.gallery_subtitle} />
        </Suspense>
      ),
    },
    {
      id: 'partners',
      label: 'Partners',
      visible: data.partners.length > 0 || data.testimonials.length > 0,
      render: (tone) => (
        <PartnersTestimonials
          partners={data.partners}
          testimonials={data.testimonials}
          tone={tone}
          title={s.partners_title}
          subtitle={s.partners_subtitle}
          partnersLabel={s.partners_label}
        />
      ),
    },
    {
      id: 'contact',
      label: 'Contact',
      visible: true,
      render: (tone) => <Contact settings={s} tone={tone} title={s.contact_title} subtitle={s.contact_subtitle} />,
    },
  ]

  const shown = sections.filter((s) => s.visible)
  // Partners is a supporting section, so it stays out of the navigation.
  const links = shown.filter((s) => s.id !== 'partners').map(({ id, label }) => ({ id, label }))
  const whatsapp = whatsappUrl(data.settings.whatsapp, name)

  return (
    <>
      <Seo data={data} />
      <a
        href="#main"
        className="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[60] focus:rounded-lg focus:bg-gold focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-navy"
      >
        Skip to content
      </a>
      <Navbar name={name} links={links} />
      <main id="main" tabIndex={-1} className="outline-none">
        <Hero settings={data.settings} showVentures={data.ventures.length > 0} />
        {/* Alternate white and cream down the page, whichever sections are visible. */}
        {shown.map((section, i) => (
          <div key={section.id}>{section.render(i % 2 === 0 ? 'white' : 'cream')}</div>
        ))}
      </main>
      {whatsapp && <FloatingWhatsApp href={whatsapp} />}
      <Footer settings={data.settings} links={links} />
    </>
  )
}
