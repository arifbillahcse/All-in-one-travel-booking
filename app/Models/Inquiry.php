<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    public const TYPE_BOOKING = 'booking';

    public const TYPE_CONTACT = 'contact';

    public const STATUSES = ['new', 'contacted', 'confirmed', 'cancelled'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'guests' => 'integer',
            'estimated_total' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
