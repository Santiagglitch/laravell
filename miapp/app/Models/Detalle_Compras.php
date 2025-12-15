<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_Compras extends Model
{
    protected $table = 'detalle_compras';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'Fecha_Entrada',
        'Cantidad',
        'ID_Proveedor',
        'ID_Entrada'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'ID_Proveedor', 'ID_Proveedor');
    }

    public function compras()
    {
        return $this->belongsTo(Compras::class, 'ID_Entrada', 'ID_Entrada');
    }
}
