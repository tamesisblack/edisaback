<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'nombre',
    //     'apellido',
    //     'telefono',
    //     'cedula',
    //     'cuenta',
    //     'tipocuenta',
    //     'fnacimiento',
    //     'email',
    //     'password',
    //     'estado',
    //     'rol_id',        
    //     'banco_id',        
    // ];
    protected $table = "usuario";
    protected $primaryKey = 'idusuario';
    
    protected $fillable = [
        'nombre','apellido','cedula','email','password','telefono','estado','genero','institucion_idinstitucion','grupo_idgrupo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
