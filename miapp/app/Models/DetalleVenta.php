<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'Detalle_Ventas';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ID_Venta',
        'ID_Producto',
        'Cantidad',
        'Fecha_Salida'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta', 'ID_Venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto', 'ID_Producto');
    }
}