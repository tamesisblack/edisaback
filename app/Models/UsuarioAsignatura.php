<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class UsuarioAsignatura extends Model
{
    protected $table = "usuario_has_asignatura";
    protected $primaryKey = 'usuario_idusuario';
    protected $fillable = [
        'usuario_idusuario','asignatura_idasignatura'
    ];
    public $timestamps = false;
}
