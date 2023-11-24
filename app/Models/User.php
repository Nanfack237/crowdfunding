<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'country',
        'city',
        'password',
        'status'
    ];

    public function donations(){
        return $this->hasMany(Donation::class);
    }
}
