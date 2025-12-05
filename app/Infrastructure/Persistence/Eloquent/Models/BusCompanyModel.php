<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $primary_color
 * @property string $secondary_color
 * @property string $text_color
 */
final class BusCompanyModel extends Model
{
    protected $table = 'bus_companies';

    protected $fillable = [
        'code',
        'name',
        'primary_color',
        'secondary_color',
        'text_color',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(BusLineModel::class, 'company_id');
    }
}
