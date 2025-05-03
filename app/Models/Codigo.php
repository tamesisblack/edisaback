<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Codigo extends Model
{
    protected $table = "codigos";
    protected $primaryKey = 'codigo_id';
    protected $fillable = [
        '_id','idlibro','id_verificador','detalle','url','idcreate','nombre','estado',"id_verificacion"
    ];
}
