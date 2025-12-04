<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    protected $table = 'compras';

    protected $primaryKey = 'ID_Entrada';
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ID_Entrada',
        'Precio_Compra',
        'ID_Producto',
        'Documento_Empleado'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto', 'ID_Producto');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Documento_Empleado', 'Documento_Empleado');
    }
}
