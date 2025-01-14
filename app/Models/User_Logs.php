<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Logs extends Model
{
    use HasFactory;

    protected $fillable = [
        'mac_address',
        'device_name',
        'user_id'
    ];
}
