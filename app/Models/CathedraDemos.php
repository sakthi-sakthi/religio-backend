<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CathedraDemos extends Model
{
    use HasFactory;

    protected $table = 'cathedra_demo';

    protected $fillable = [
        'id',
        'name',
        'email',
        'mobile',
    ];
}
