<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\EventFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'starts_at'];

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsBetween($query, $startsAt, $endsAt)
    {
        if ($startsAt) {
            $query->where('events.starts_at', '>', Carbon::createFromFormat('Y-m-d H:i', "{$startsAt} 00:00"));
        }
        if ($endsAt) {
            $query->where('events.starts_at', '<', Carbon::createFromFormat('Y-m-d H:i', "{$endsAt} 23:59"));
        }
        return $query;
    }

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('starts_at');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return EventFactory::new();
    }
}
