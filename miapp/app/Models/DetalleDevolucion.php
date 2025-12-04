<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    // Nombre real de la tabla
    protected $table = 'detalle_devoluciones';

    //Key primaria definida
    protected $primaryKey = 'ID_DetalleDev';

    // No tiene timestamps
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'ID_DetalleDev',
        'ID_Devolucion',
        'Cantidad_Devuelta',
        'ID_Venta'
    ];

    // Relaciones
    public function Devoluciones()
    {
        return $this->belongsTo(Devolucion::class, 'ID_Devolucion', 'ID_Devolucion');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta', 'ID_Venta');
    }
}
