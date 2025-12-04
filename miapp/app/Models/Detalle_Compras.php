<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_Compras extends Model
{
    // Nombre real de la tabla
    protected $table = 'detalle_compras';

    // Esta tabla NO tiene clave primaria definida
    protected $primaryKey = null;
    public $incrementing = false;

    // No tiene timestamps
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'Fecha_Entrada',
        'Cantidad',
        'ID_Proveedor',
        'ID_Entrada'
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'ID_Proveedor', 'ID_Proveedor');
    }

    public function compras()
    {
        return $this->belongsTo(Compras::class, 'ID_Entrada', 'ID_Entrada');
    }
}
