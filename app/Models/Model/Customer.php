<?php

namespace App\Models\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'fullname', 'email','phone','adress','photo'
    ];
}
