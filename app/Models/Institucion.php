<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = "institucion";
    protected $primaryKey = 'idinstitucion';
    protected $fillable = [
       'temporal_id',  'nombre','detalle','region','idprovincia','idcanton','idparroquia','estado'
    ];
	public $timestamps = false;
}
