New message from the website contact form.

Name:  {!! $submission->name !!}
Email: {!! $submission->email !!}
Page:  {!! url($submission->page ?: '/') !!}
Sent:  {{ $submission->created_at->timezone('Europe/London')->format('j M Y, H:i') }}

{!! $submission->message !!}

--
Reply to this email to answer them directly. Every message is also kept in the admin:
{{ url('/admin/contact-submissions') }}
