<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'detalle_devoluciones';

    protected $primaryKey = 'ID_DetalleDev';

    public $timestamps = false;

    protected $fillable = [
        'ID_DetalleDev',
        'ID_Devolucion',
        'Cantidad_Devuelta',
        'ID_Venta'
    ];

    public function devoluciones()
    {
        return $this->belongsTo(Devolucion::class, 'ID_Devolucion', 'ID_Devolucion');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta', 'ID_Venta');
    }
}
