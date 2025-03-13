<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;

    protected $table = "tbl_booking_request";

    public function loads(){
        return $this->hasMany(BookingLoad::class,'BookingRequestID','BookingRequestID');
    }

    public function booking(){
        return $this->belongsTo(Booking::class,'BookingRequestID','BookingRequestID');
    }

    public function invoice_items(){
        return $this->hasMany(BookingInvoiceItem::class,'InvoiceID','InvoiceID');
    }

    public function invoice(){
        return $this->hasMany(BookingInvoice::class,'BookingRequestID','BookingRequestID');
    }
}
