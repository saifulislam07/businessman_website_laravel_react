import { MessageCircle } from 'lucide-react'

/** Round WhatsApp shortcut pinned to the bottom-right; shown on mobile only. */
export function FloatingWhatsApp({ href }: { href: string }) {
  return (
    <aside aria-label="Quick contact">
      <a
        href={href}
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat on WhatsApp (opens in a new tab)"
        className="fixed bottom-4 right-4 z-30 flex h-14 w-14 items-center justify-center rounded-full bg-[#1a7a43] text-white shadow-lg md:hidden"
      >
        <MessageCircle aria-hidden="true" className="h-7 w-7" />
      </a>
    </aside>
  )
}
