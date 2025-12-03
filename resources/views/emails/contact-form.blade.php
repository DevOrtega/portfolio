<x-mail::message>
# Mensaje de contacto

Has enviado un mensaje desde el formulario de contacto. Te responderé lo antes posible.

---

**De:** {{ $name }}  
**Email:** {{ $email }}  
**Asunto:** {{ $subject }}

---

## Mensaje:

{{ $messageContent }}

---

Gracias,<br>
Carlos Ortega
</x-mail::message>
