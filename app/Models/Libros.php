<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Libros extends Model
{
    protected $table = "libros";
    protected $primaryKey = 'libro_id';
    protected $fillable = [
        '_id','idlibro','nombre','cantidad','prefijo'
    ];
}
