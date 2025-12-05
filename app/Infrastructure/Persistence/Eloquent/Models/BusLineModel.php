<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $company_id
 * @property string $line_number
 * @property string $type
 * @property string $origin
 * @property string $destination
 * @property string|null $color
 * @property bool $is_main_line
 */
final class BusLineModel extends Model
{
    protected $table = 'bus_lines';

    protected $fillable = [
        'company_id',
        'line_number',
        'type',
        'origin',
        'destination',
        'color',
        'is_main_line',
    ];

    protected $casts = [
        'is_main_line' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(BusCompanyModel::class, 'company_id');
    }

    public function stops(): BelongsToMany
    {
        return $this->belongsToMany(
            BusStopModel::class,
            'bus_route_stops',
            'line_id',
            'stop_id'
        )->withPivot(['direction', 'order'])
         ->orderByPivot('order');
    }

    public function stopsOutbound(): BelongsToMany
    {
        return $this->stops()
            ->wherePivot('direction', 'outbound')
            ->orderByPivot('order');
    }

    public function stopsInbound(): BelongsToMany
    {
        return $this->stops()
            ->wherePivot('direction', 'inbound')
            ->orderByPivot('order');
    }
}
