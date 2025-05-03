<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = "contratos";
    protected $primaryKey = 'contrato_id';
    protected $fillable = [
        'cod_contrato','ciudad','year','id_temporada','id_institucion','id_docente','estado','id_periodo','verificacion_estado'
    ];
}
