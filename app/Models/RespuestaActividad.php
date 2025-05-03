<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaActividad extends Model
{
    use HasFactory;
    protected $table = "respuesta_actividad";
    protected $primaryKey = 'res_actividad_id';
    protected $fillable = [
        'actividad_id','nombre_tarea','url','nota','descripcion','fecha_create','usuario_idusuario','year','mes','comentario'
    ];
	public $timestamps = false;
}
