<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'tbl_booking1';
    protected $primaryKey = 'BookingID';

    protected $fillable = [
        'BookingID',
        'BookingRequestID',
        'BookingType',
        'CompanyName',
        'OpportunityName',
    ];
    public $timestamps = false;
    public function loads(){
        return $this->hasMany(BookingLoad::class,'BookingID','BookingID');
    }

    public function bookingRequest(){
        return $this->belongsTo(BookingRequest::class,'BookingRequestID','BookingRequestID');
    }

    public function invoice_items(){
        return $this->hasMany(BookingInvoiceItem::class,'BookingID','BookingID');
    }
}
   