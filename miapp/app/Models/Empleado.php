<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'Empleados';

    protected $primaryKey = 'Documento_Empleado';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

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

    public function contrasena()
    {
        return $this->hasOne(Contrasena::class, 'Documento_Empleado', 'Documento_Empleado');
    }
}
