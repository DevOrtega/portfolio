<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class BusRouteStopModel extends Model
{
    protected $table = 'bus_route_stops';
    protected $guarded = [];

    public function line()
    {
        return $this->belongsTo(BusLineModel::class, 'line_id');
    }

    public function stop()
    {
        return $this->belongsTo(BusStopModel::class, 'stop_id');
    }
}
