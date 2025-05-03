<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioHasLibro extends Model
{
    use HasFactory;
    protected $table = "usuario_has_libro";
    protected $primaryKey = 'id';
    protected $fillable = [
        'asignatura_idasignatura','usuario_idusuario','estado'
    ];
    public $timestamps = false;
}
