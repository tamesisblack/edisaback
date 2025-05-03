<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = "grupo";
    protected $primaryKey = 'idgrupo';
    protected $fillable = [
        'nombre','detalle','fecha_create','estado'
    ];
	public $timestamps = false;
}
