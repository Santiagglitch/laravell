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
      public function estado()
    {
        return $this->belongsTo(Estado::class, 'ID_Estado', 'ID_Estado');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'ID_Rol', 'ID_Rol');
    }
}
