<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $primaryKey = 'ID_Proveedor';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ID_Proveedor',
        'Nombre_Proveedor',
        'Correo_Electronico',
        'Telefono',
        'ID_Estado'
    ];
}
