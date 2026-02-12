<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $primaryKey = 'ID_Venta';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Documento_Cliente',
        'Documento_Empleado'
    ];

    /**
     * Relación con DetalleVenta
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'ID_Venta', 'ID_Venta');
    }

    /**
     * Relación con Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Documento_Cliente', 'Documento_Cliente');
    }

    /**
     * Relación con Empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Documento_Empleado', 'Documento_Empleado');
    }
}