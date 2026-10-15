/** Builds a wa.me link from a phone number in any common format; null when there are no digits. */
export function whatsappUrl(number: string | undefined, name: string): string | null {
  const digits = (number ?? '').replace(/\D/g, '')
  if (!digits) return null
  const text = encodeURIComponent(`Hello ${name}, I found your website and would like to get in touch.`)
  return `https://wa.me/${digits}?text=${text}`
}
