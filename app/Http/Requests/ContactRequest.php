<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-\.]+$/u', // Only letters, spaces, hyphens, dots
            ],
            'email' => [
                'required',
                'email:rfc,dns', // Strict email validation with DNS check
                'max:255',
            ],
            'subject' => [
                'required',
                'string',
                'min:5',
                'max:200',
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
            // Honeypot field - should be empty (bots fill this)
            'website' => [
                'max:0',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras, espacios, guiones y puntos.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede exceder 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor, introduce un correo electrónico válido.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.min' => 'El asunto debe tener al menos 5 caracteres.',
            'subject.max' => 'El asunto no puede exceder 200 caracteres.',
            'message.required' => 'El mensaje es obligatorio.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje no puede exceder 5000 caracteres.',
            'website.max' => 'Error de validación.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs to prevent XSS
        $this->merge([
            'name' => $this->sanitizeInput($this->name),
            'subject' => $this->sanitizeInput($this->subject),
            'message' => $this->sanitizeInput($this->message),
        ]);
    }

    /**
     * Sanitize input string.
     */
    private function sanitizeInput(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Remove HTML tags and encode special characters
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove potential SQL injection patterns
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        
        return trim($value);
    }
}
