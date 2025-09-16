<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    public $fillable = ['dni','name', 'address', 'phone_number', 'email', 'email_secundary', 'contact_person', 'highlights'];
}
