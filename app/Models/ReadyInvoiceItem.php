<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadyInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'InvoiceID',
        'InvoiceNumber',
        'ItemNumber',
        'Qty',
        'UnitPrice',
        'GrossAmount',
        'TaxAmount',
        'TaxRate',
        'NetAmount',
        'NominalCode',
        'StockCode',
        'Description',
        'Comment1',
        'Comment2',
        'CreateDateTime',
        'UpdateDateTime'
    ];

    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo(ReadyInvoice::class, 'InvoiceID', 'id');
    }
}
