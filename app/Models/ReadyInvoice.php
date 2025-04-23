<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'BookingRequestID',
        'CompanyID',
        'CompanyName',
        'OpportunityID',
        'OpportunityName',
        'InvoiceType',
        'ContactID',
        'ContactName',
        'ContactMobile',
        'SubTotalAmount',
        'VatAmount',
        'FinalAmount',
        'TaxRate',
        'CreatedUserID',
        'Status',
        'Comment',
        'InvoiceDate',
        'InvoiceNumber'
    ];

    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(ReadyInvoiceItem::class, 'InvoiceID', 'InvoiceID');
    }


    public function booking()
    {
        return $this->hasMany(Booking::class, 'BookingRequestID', 'BookingRequestID');
    }
}
