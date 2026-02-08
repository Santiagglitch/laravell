<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'Detalle_Devoluciones';

    protected $primaryKey = 'ID_Devolucion';
    public $incrementing = false;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ID_Devolucion',
        'ID_Venta',
        'Cantidad_Devuelta'
    ];

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'ID_Devolucion', 'ID_Devolucion');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta', 'ID_Venta');
    }
}
