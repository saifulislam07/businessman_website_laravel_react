import { cn } from '../lib/cn'

interface RichTextProps {
  /** HTML from the API. The backend sanitizes it down to paragraphs, lists, links and basic formatting. */
  html: string
  className?: string
}

/** Renders admin-written formatted text with the site's typography. */
export function RichText({ html, className }: RichTextProps) {
  return <div className={cn('rich', className)} dangerouslySetInnerHTML={{ __html: html }} />
}
