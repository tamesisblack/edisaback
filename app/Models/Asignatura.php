<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    protected $table = "asignatura";
    protected $primaryKey = 'idasignatura';
    protected $fillable = [
        'nombre','detalle','nivel','area_idarea','estado'
    ];
	public $timestamps = false;
}
