<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingInvoice extends Model
{
    use HasFactory;

    protected $table = 'tbl_booking_invoice';
    protected $primaryKey = 'InvoiceId';
    public $timestamps = false;
    public function booking()
    {
        return $this->hasMany(Booking::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function invoice_items()
    {
        return $this->hasMany(BookingInvoiceItem::class, 'InvoiceID', 'InvoiceID');
    }

    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function originalInvoice()
    {
        return $this->belongsTo(BookingInvoice::class, 'parent_invoice_id');
    }

    public function splitInvoices()
    {
        return $this->hasMany(BookingInvoice::class, 'parent_invoice_id');
    }

}
