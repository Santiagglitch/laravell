<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    // Nombre real de la tabla
    protected $table = 'proveedores';

    // Clave primaria
    protected $primaryKey = 'ID_Proveedor';

    // La PK es VARCHAR, no autoincrementa
    public $incrementing = false;
    protected $keyType = 'string';

    // La tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'ID_Proveedor',
        'Nombre_Proveedor',
        'Correo_Electronico',
        'Telefono',
        'ID_Estado'
    ];

 
}