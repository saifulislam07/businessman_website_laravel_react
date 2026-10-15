/** Plain text from an HTML string (for meta tags and structured data). */
export function stripHtml(html: string | null | undefined): string | undefined {
  if (!html) return undefined
  const text = new DOMParser().parseFromString(html, 'text/html').body.textContent ?? ''
  return text.replace(/\s+/g, ' ').trim() || undefined
}
