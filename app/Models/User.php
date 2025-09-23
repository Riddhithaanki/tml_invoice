<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "tbl_users";

    protected $fillable = [
        'email', 'password', 'roleId', // add other fields you use
    ];


    const CREATED_AT = 'createdDtm';
    const UPDATED_AT = 'updatedDtm';

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $primaryKey = 'userId'; // if your PK is not 'id'

    public function isAdmin()
    {
        return $this->roleId === 1;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId','roleId');
    }
}
