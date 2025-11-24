<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Education Model
 * 
 * @property int $id
 * @property string $institution
 * @property string $degree
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $description
 */
final class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution',
        'degree',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'institution' => 'string',
            'degree' => 'string',
            'start_date' => 'string',
            'end_date' => 'string',
            'description' => 'string',
        ];
    }
}
