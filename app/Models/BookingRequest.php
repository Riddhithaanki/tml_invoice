<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;

    protected $table = "tbl_booking_request";

    protected $fillable = [
        'BookingRequestID',
        'is_delete', // Add this field
        // Add other fillable fields here if necessary
    ];

    public $timestamps = false;

    public function loads()
    {
        return $this->hasMany(BookingLoad::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function invoice_items()
    {
        return $this->hasMany(BookingInvoiceItem::class, 'InvoiceID', 'InvoiceID');
    }

    public function invoice()
    {
        return $this->hasMany(BookingInvoice::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function invoices()
    {
        return $this->hasMany(BookingInvoice::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function originalInvoices()
    {
        return $this->hasMany(BookingInvoice::class, 'BookingRequestID', 'BookingRequestID')
            ->where('is_split', 0);
    }

    public function splitInvoices()
    {
        return $this->hasMany(BookingInvoice::class, 'BookingRequestID', 'BookingRequestID')
            ->where('is_split', 1);
    }

}
