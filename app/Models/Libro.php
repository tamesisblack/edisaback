<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $table = "libro";
    protected $primaryKey = 'idlibro';
    protected $fillable = [
        'nombre','detalle','url','fecha_create','libro_id','asignatura_idasignatura','estado','id_area','c_primaria'
    ];
	public $timestamps = false;
}
