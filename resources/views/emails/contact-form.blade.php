@php
$isEnglish = ($locale ?? 'es') === 'en';
@endphp

<x-mail::message>
# {{ $isEnglish ? 'Contact Message' : 'Mensaje de contacto' }}

{{ $isEnglish ? 'You have sent a message from the contact form. I will reply as soon as possible.' : 'Has enviado un mensaje desde el formulario de contacto. Te responderé lo antes posible.' }}

---

**{{ $isEnglish ? 'From' : 'De' }}:** {{ $name }}  
**Email:** {{ $email }}  
**{{ $isEnglish ? 'Subject' : 'Asunto' }}:** {{ $subject }}

---

## {{ $isEnglish ? 'Message' : 'Mensaje' }}:

{{ $messageContent }}

---

{{ $isEnglish ? 'Thanks' : 'Gracias' }},<br>
Carlos Ortega
</x-mail::message>
