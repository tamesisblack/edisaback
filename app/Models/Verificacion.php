<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Verificacion extends Model
{
    protected $table = "verificacion_codigo";
    protected $primaryKey = 'verificacion_id';
    protected $fillable = [
        'descripcion',
        'institucion_id',
        'docente_id',
        'contrato',
        'id_periodo',
        'fecha_inicio',
        'fecha_finalizacion',
        'codigos',
        'estado',
    ];
}
