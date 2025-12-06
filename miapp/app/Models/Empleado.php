<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    // Nombre real de la tabla
    protected $table = 'empleados';

    // Clave primaria (tipo string)
    protected $primaryKey = 'Documento_Empleado';
    public $incrementing = false;
    protected $keyType = 'string';

    // La tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'Documento_Empleado',
        'Tipo_Documento',
        'Nombre_Usuario',
        'Apellido_Usuario',
        'Edad',
        'Correo_Electronico',
        'Telefono',
        'Genero',
        'ID_Estado',
        'ID_Rol',
        'Fotos'
    ];
}