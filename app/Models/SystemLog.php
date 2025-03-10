<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $table = 'tbl_system_logs';

    // Allow mass assignment for these fields
    protected $fillable = [
        'user_id',
        'activity',
        'details'
    ];
}
