<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Experience Model
 * 
 * @property int $id
 * @property string $company
 * @property string $role
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $description
 */
final class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'role',
        'role_en',
        'start_date',
        'end_date',
        'description',
        'description_en',
    ];

    protected function casts(): array
    {
        return [
            'company' => 'string',
            'role' => 'string',
            'start_date' => 'string',
            'end_date' => 'string',
            'description' => 'string',
        ];
    }

    /**
     * Get localized data based on current locale
     */
    public function toArray(): array
    {
        $locale = app()->getLocale();
        $data = parent::toArray();

        if ($locale === 'en') {
            $data['role'] = $this->role_en ?? $this->role;
            $data['description'] = $this->description_en ?? $this->description;
        }

        // Remove _en fields from response
        unset($data['role_en'], $data['description_en']);

        return $data;
    }
}
