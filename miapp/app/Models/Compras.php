<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    protected $table = 'compras';

    protected $primaryKey = 'ID_Entrada';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Precio_Compra',
        'ID_Producto',
        'Documento_Empleado'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Documento_Empleado', 'Documento_Empleado');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle_Compras::class, 'ID_Entrada', 'ID_Entrada');
    }

    // Accessor para obtener el nombre del producto desde la tabla
    public function getNombreProductoAttribute()
    {
        $producto = \DB::table('productos')
            ->where('ID_Producto', $this->ID_Producto)
            ->first();
        
        return $producto ? $producto->Nombre_Producto : 'N/A';
    }
}