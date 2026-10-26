<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItineraryItem extends Model
{
    protected $fillable = [
        'itinerary_id',
        'destination_id',
        'day_number',
        'start_time',
        'duration_minutes',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'duration_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Itinerary, $this>
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * @return BelongsTo<Destination, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function endTime(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->start_time)->addMinutes($this->duration_minutes);
    }
}
