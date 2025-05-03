<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class PeriodosInstitucion extends Model
{
    protected $table = "periodoescolar_has_institucion";
    protected $primaryKey = 'id';
    protected $fillable =["periodoescolar_idperiodoescolar","institucion_idInstitucion"];
}
