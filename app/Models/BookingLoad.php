<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingLoad extends Model
{
    use HasFactory;

    protected $table = "tbl_booking_loads1";

    protected $primaryKey = "LoadID";

    public $timestamps = false;

    protected $fillable = [
        'InvoiceType',
        'BookingRequestID',
        'BookingID',
        'InvoiceDate',
        'InvoiceNumber',
        'CompanyID',
        'CompanyName',
        'OpportunityID',
        'OpportunityName',
        'ContactID',
        'ContactName',
        'ContactMobile',
        'SubTotalAmount',
        'VatAmount',
        'FinalAmount',
        'TaxRate',
        'Status',
        'CreatedUserID',
        'CreateDateTime',
        'UpdateDateTime',
        'is_split',
        'parent_invoice_id',
    ];

    public function parentInvoice()
    {
        return $this->belongsTo(BookingLoad::class, 'parent_invoice_id', 'LoadID');
    }

    public function childInvoices()
    {
        return $this->hasMany(BookingLoad::class, 'parent_invoice_id', 'LoadID');
    }


}
