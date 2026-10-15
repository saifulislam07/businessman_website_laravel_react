import { Container } from './ui'

function Block({ className }: { className: string }) {
  return <div className={`animate-pulse rounded-card bg-navy/10 ${className}`} />
}

/** Full-page placeholder shown while the home data loads. */
export function LoadingSkeleton() {
  return (
    <div role="status" aria-live="polite" aria-label="Loading">
      <div className="bg-navy py-24 md:py-32">
        <Container className="grid gap-8 md:grid-cols-2">
          <div className="space-y-4">
            <div className="h-4 w-32 animate-pulse rounded bg-white/20" />
            <div className="h-12 w-3/4 animate-pulse rounded bg-white/20" />
            <div className="h-5 w-full animate-pulse rounded bg-white/20" />
            <div className="h-5 w-2/3 animate-pulse rounded bg-white/20" />
          </div>
          <div className="h-64 animate-pulse rounded-card bg-white/10 md:h-80" />
        </Container>
      </div>
      <Container className="space-y-6 py-16 md:py-24">
        <Block className="h-8 w-56" />
        <div className="grid gap-6 md:grid-cols-3">
          <Block className="h-48" />
          <Block className="h-48" />
          <Block className="h-48" />
        </div>
      </Container>
      <span className="sr-only">Loading…</span>
    </div>
  )
}
