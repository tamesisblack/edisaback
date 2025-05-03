<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Periodos extends Model
{
    protected $table = "periodoescolar";
    protected $primaryKey = 'idperiodoescolar';
    public $timestamps = false;
}
