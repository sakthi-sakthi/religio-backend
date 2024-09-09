<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cathedracontactus extends Model
{
    use HasFactory;
    protected $table = 'cathedracontactus';

    protected $fillable = [
        'id',
        'name',
        'email',
        'mobile',
        'subject',
    ];
}
