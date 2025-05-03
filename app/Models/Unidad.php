<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = "unidades_libros";
    protected $primaryKey = 'id_unidad_libro';
	public $timestamps = true;
    protected $fillable = [
        
        'id_libro',
        'unidad',
        'nombre_unidad',
        'pag_inicio',
        'pag_fin',
        'estado',
        
    ];
}
