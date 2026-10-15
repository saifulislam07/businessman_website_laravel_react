import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { StrictMode } from 'react'
import { createRoot, hydrateRoot } from 'react-dom/client'
import { HelmetProvider } from 'react-helmet-async'
import './index.css'
import App from './App.tsx'
import { readPrerenderedData } from './lib/prerendered'

const queryClient = new QueryClient({
  defaultOptions: { queries: { retry: 1, refetchOnWindowFocus: false } },
})

const app = (
  <StrictMode>
    <HelmetProvider>
      <QueryClientProvider client={queryClient}>
        <App />
      </QueryClientProvider>
    </HelmetProvider>
  </StrictMode>
)

const root = document.getElementById('root')!

// Prerendered pages are hydrated in place; the dev server (no prerendered markup) renders from scratch.
if (root.hasChildNodes() && readPrerenderedData()) {
  hydrateRoot(root, app)
} else {
  createRoot(root).render(app)
}
