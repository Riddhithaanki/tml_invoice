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
    public function booking() {
        return $this->hasMany(Booking::class, 'BookingRequestID', 'BookingRequestID');
    }

    public function invoice_items(){
        return $this->hasMany(BookingInvoiceItem::class,'InvoiceID','InvoiceID');
    }

}
