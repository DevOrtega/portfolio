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
        'start_date',
        'end_date',
        'description',
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
}
