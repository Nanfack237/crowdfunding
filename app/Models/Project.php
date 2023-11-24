<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',
        'description',
        'category',
        'user_id',
        'target_amount',
        'start_date',
        'end_date',
        'current_amount',
        'status'

    ];
    
    public function user(){

        return $this->belongsTo(User::class);

    }

    public function donations(){

        return $this->hasMany(Donation::class);

    }
}
