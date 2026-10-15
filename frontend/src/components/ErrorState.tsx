import { Button, Container } from './ui'

interface ErrorStateProps {
  onRetry: () => void
  retrying?: boolean
}

export function ErrorState({ onRetry, retrying = false }: ErrorStateProps) {
  return (
    <Container className="flex min-h-screen flex-col items-center justify-center text-center">
      <h1 className="text-3xl font-semibold md:text-4xl">We couldn't load the page</h1>
      <p className="mt-4 max-w-md text-muted">
        Something went wrong while contacting the server. Please check your connection and try again.
      </p>
      <Button className="mt-8" onClick={onRetry} disabled={retrying}>
        {retrying ? 'Retrying…' : 'Try again'}
      </Button>
    </Container>
  )
}
