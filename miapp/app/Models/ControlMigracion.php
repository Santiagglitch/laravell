<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'Detalle_Devoluciones';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;
    
    protected $fillable = [
        'ID_Devolucion',
        'ID_Venta',
        'Cantidad_Devuelta'
    ];
    
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'ID_Devolucion', 'ID_Devolucion');
    }
}