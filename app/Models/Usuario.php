<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = "usuario";
    protected $primaryKey = 'idusuario';
    protected $fillable = [
        'persona_id','nombre','verificacion_estado','apellido','cedula','email','password','telefono','estado','genero','institucion_idinstitucion','grupo_idgrupo'
    ];
	public $timestamps = false;
}
