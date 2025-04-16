<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDifference extends Model
{
    protected $fillable = [
        'invoice_number',
        'basic_details_differences',
        'items_differences',
        'status',
        'resolution_notes'
    ];

    protected $casts = [
        'basic_details_differences' => 'array',
        'items_differences' => 'array'
    ];
}
