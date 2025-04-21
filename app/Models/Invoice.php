<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'reference',
        'customer_id',  // Foreign key to companies table
        'status',
        'type',
        'date',
        'tax_date',
        'net_total',
        'discount_total',
        'charges_total',
        'tax_total',
        'gross_total',
        'currency',
        'exchange_rate',
        'customer_reference',
        'sales_order_reference',
        'notes',
        'billing_address',
        'shipping_address',
        'isApproved',
    ];

    // Relationship to Company (optional)
    public function company()
    {
        return $this->belongsTo(Company::class, 'customer_id');
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_uuid', 'uuid');
    }

}
