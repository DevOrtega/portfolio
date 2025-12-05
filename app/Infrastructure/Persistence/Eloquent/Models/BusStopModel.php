<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $zone
 * @property float $lat_outbound
 * @property float $lng_outbound
 * @property float $lat_inbound
 * @property float $lng_inbound
 */
final class BusStopModel extends Model
{
    protected $table = 'bus_stops';

    protected $fillable = [
        'code',
        'name',
        'zone',
        'lat_outbound',
        'lng_outbound',
        'lat_inbound',
        'lng_inbound',
    ];

    protected $casts = [
        'lat_outbound' => 'float',
        'lng_outbound' => 'float',
        'lat_inbound' => 'float',
        'lng_inbound' => 'float',
    ];

    public function lines(): BelongsToMany
    {
        return $this->belongsToMany(
            BusLineModel::class,
            'bus_route_stops',
            'stop_id',
            'line_id'
        )->withPivot(['direction', 'order']);
    }
}
