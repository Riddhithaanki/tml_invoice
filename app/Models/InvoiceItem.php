<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'invoice_uuid',
        'reference',
        'number',
        'description',
        'quantity',
        'unit_price',
        'net_amount',
        'discount_total',
        'tax_total',
        'gross_total',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_uuid', 'uuid');
    }

}
