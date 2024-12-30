<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'reply',
        'proof',
        'buyer_name',
        'userID'
    ];

    public function user()
        {
            return $this->belongsTo(User::class, 'userID');
        }
}
