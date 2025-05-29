<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRelationship extends Model
{
    protected $fillable = [
        'source_booking_id',
        'target_booking_id',
        'relationship_type',
        'metadata',
        'relationship_date',
        'created_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'relationship_date' => 'datetime'
    ];

    public function sourceBooking()
    {
        return $this->belongsTo(Booking::class, 'source_booking_id', 'BookingID');
    }

    public function targetBooking()
    {
        return $this->belongsTo(Booking::class, 'target_booking_id', 'BookingID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}