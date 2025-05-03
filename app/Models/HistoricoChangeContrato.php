<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoChangeContrato extends Model
{
    use HasFactory;
    protected $table = 'his_change_contrato';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contrato',
        'docente_anterior', 
        'docente_nuevo',
        'usuario_editor',
        'usuario_editor', 
        'institucion_id',
        'periodo_id',
        'observacion',
    ];
}
