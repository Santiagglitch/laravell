<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrasena extends Model
{
    protected $table = 'Contrasenas';
    protected $primaryKey = 'ID_Contrasena';
    public $timestamps = false;

    protected $fillable = [
        'Documento_Empleado',
        'Contrasena_Hash',
        'Fecha_Creacion'
    ];
}
