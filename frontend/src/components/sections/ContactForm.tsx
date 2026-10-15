import { zodResolver } from '@hookform/resolvers/zod'
import { useMutation } from '@tanstack/react-query'
import { isAxiosError } from 'axios'
import { CheckCircle2, Loader2 } from 'lucide-react'
import { useState, type ReactNode } from 'react'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import { postContact } from '../../api'
import { cn } from '../../lib/cn'
import type { ContactPayload } from '../../types'
import { Turnstile } from '../Turnstile'
import { Button } from '../ui'

const schema = z.object({
  name: z.string().trim().min(1, 'Please enter your name.').max(100, 'Name is too long.'),
  email: z.string().trim().min(1, 'Please enter your email.').email('Please enter a valid email address.'),
  phone: z.string().trim().max(30, 'Phone number is too long.').optional(),
  subject: z.string().trim().min(2, 'Please enter a subject.').max(150, 'Subject is too long.'),
  message: z.string().trim().min(1, 'Please write a message.').max(2000, 'Message must be 2000 characters or fewer.'),
  // Honeypot: hidden from people; the server rejects the request when it is filled.
  website: z.string().optional(),
})

type FormValues = z.infer<typeof schema>

const TURNSTILE_SITE_KEY = import.meta.env.VITE_TURNSTILE_SITE_KEY as string | undefined

const inputClass =
  'w-full rounded-lg border border-navy/20 bg-white px-4 py-3 text-ink placeholder:text-muted/70 focus:border-navy aria-[invalid=true]:border-red-700'

function Field({ id, label, optional, error, children }: { id: string; label: string; optional?: boolean; error?: string; children: ReactNode }) {
  return (
    <div>
      <label htmlFor={id} className="mb-1.5 block text-sm font-medium text-navy">
        {label}
        {optional && <span className="font-normal text-muted"> (optional)</span>}
      </label>
      {children}
      {error && (
        <p id={`${id}-error`} role="alert" className="mt-1.5 text-sm text-red-700">
          {error}
        </p>
      )}
    </div>
  )
}

export function ContactForm() {
  const [sent, setSent] = useState<string | null>(null)
  const [formError, setFormError] = useState<string | null>(null)
  const [turnstileToken, setTurnstileToken] = useState<string | null>(null)
  const [turnstileReset, setTurnstileReset] = useState(0)

  const {
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<FormValues>({ resolver: zodResolver(schema) })

  const mutation = useMutation({
    mutationFn: (payload: ContactPayload) => postContact(payload),
    onSuccess: (res) => setSent(res.message),
    onError: (error) => {
      setTurnstileReset((n) => n + 1)

      if (isAxiosError(error) && error.response?.status === 422) {
        const serverErrors = (error.response.data?.errors ?? {}) as Record<string, string[]>
        let unmatched = false
        for (const [field, messages] of Object.entries(serverErrors)) {
          if (field in schema.shape && field !== 'website') {
            setError(field as keyof FormValues, { type: 'server', message: messages[0] })
          } else {
            unmatched = true
            setFormError(messages[0])
          }
        }
        if (!unmatched) setFormError(null)
        return
      }

      if (isAxiosError(error) && error.response?.status === 429) {
        setFormError('Too many attempts, please try again in a minute.')
        return
      }

      setFormError('Something went wrong while sending your message. Please try again.')
    },
  })

  const onSubmit = (values: FormValues) => {
    setFormError(null)

    if (TURNSTILE_SITE_KEY && !turnstileToken) {
      setFormError('Please complete the verification below.')
      return
    }

    mutation.mutate({
      name: values.name,
      email: values.email,
      phone: values.phone || undefined,
      subject: values.subject,
      message: values.message,
      website: values.website ?? '',
      turnstile_token: turnstileToken ?? undefined,
    })
  }

  if (sent) {
    return (
      <div role="status" className="rounded-card border border-navy/10 bg-white p-8 text-center">
        <CheckCircle2 aria-hidden="true" className="mx-auto h-12 w-12 text-gold-dark" />
        <h3 className="mt-4 text-2xl font-semibold">Thank you</h3>
        <p className="mt-2 text-muted">{sent}</p>
      </div>
    )
  }

  const describe = (field: keyof FormValues) => (errors[field] ? `${field}-error` : undefined)

  return (
    <form onSubmit={handleSubmit(onSubmit)} noValidate className="space-y-5 rounded-card border border-navy/10 bg-white p-6 md:p-8">
      <div className="grid gap-5 sm:grid-cols-2">
        <Field id="name" label="Name" error={errors.name?.message}>
          <input id="name" autoComplete="name" className={inputClass} aria-invalid={!!errors.name} aria-describedby={describe('name')} {...register('name')} />
        </Field>
        <Field id="email" label="Email" error={errors.email?.message}>
          <input id="email" type="email" autoComplete="email" className={inputClass} aria-invalid={!!errors.email} aria-describedby={describe('email')} {...register('email')} />
        </Field>
      </div>

      <div className="grid gap-5 sm:grid-cols-2">
        <Field id="phone" label="Phone" optional error={errors.phone?.message}>
          <input id="phone" type="tel" autoComplete="tel" className={inputClass} aria-invalid={!!errors.phone} aria-describedby={describe('phone')} {...register('phone')} />
        </Field>
        <Field id="subject" label="Subject" error={errors.subject?.message}>
          <input id="subject" className={inputClass} aria-invalid={!!errors.subject} aria-describedby={describe('subject')} {...register('subject')} />
        </Field>
      </div>

      <Field id="message" label="Message" error={errors.message?.message}>
        <textarea id="message" rows={6} className={cn(inputClass, 'resize-y')} aria-invalid={!!errors.message} aria-describedby={describe('message')} {...register('message')} />
      </Field>

      {/* Honeypot: off-screen and skipped by keyboard and screen readers. */}
      <div aria-hidden="true" className="absolute -left-[9999px] h-0 w-0 overflow-hidden">
        <label htmlFor="website">Leave this field empty</label>
        <input id="website" type="text" tabIndex={-1} autoComplete="off" {...register('website')} />
      </div>

      {TURNSTILE_SITE_KEY && <Turnstile siteKey={TURNSTILE_SITE_KEY} onToken={setTurnstileToken} resetKey={turnstileReset} />}

      {formError && (
        <p role="alert" className="rounded-lg border border-red-700/30 bg-red-50 px-4 py-3 text-sm text-red-800">
          {formError}
        </p>
      )}

      <Button type="submit" className="w-full sm:w-auto" disabled={mutation.isPending}>
        {mutation.isPending && <Loader2 aria-hidden="true" className="h-4 w-4 animate-spin" />}
        {mutation.isPending ? 'Sending…' : 'Send message'}
      </Button>

      <p className="text-xs text-muted">Your details are used only to reply to your message and are never shared or sold.</p>
    </form>
  )
}
