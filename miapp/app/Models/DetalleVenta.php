<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    // Nombre real de la tabla
    protected $table = 'detalle_ventas';

    // Esta tabla NO tiene clave primaria definida
    protected $primaryKey = null;
    public $incrementing = false;

    // No tiene timestamps
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'Cantidad',
        'Fecha_Salida',
        'ID_Producto',
        'ID_Venta'
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto', 'ID_Producto');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta', 'ID_Venta');
    }
}
