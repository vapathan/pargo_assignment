<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatesHistory extends Model
{
    use HasFactory;
    protected $table='rates_history';
    protected $fillable = ['start_date', 'end_date', 'base', 'rates'];
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'rates' => 'array'
    ];
}
