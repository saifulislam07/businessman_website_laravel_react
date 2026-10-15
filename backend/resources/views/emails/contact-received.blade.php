<p>You have a new message from the website contact form.</p>

<p>
    <strong>Name:</strong> {{ $contact->name }}<br>
    <strong>Email:</strong> {{ $contact->email }}<br>
    <strong>Phone:</strong> {{ $contact->phone ?: '—' }}<br>
    <strong>Subject:</strong> {{ $contact->subject ?: '—' }}
</p>

<p style="white-space: pre-line;">{{ $contact->message }}</p>

<p style="color: #6B7280; font-size: 12px;">Sent from IP {{ $contact->ip }} at {{ $contact->created_at }}.</p>
