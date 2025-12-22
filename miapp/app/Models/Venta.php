<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $primaryKey = 'ID_Venta';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ID_Venta',
        'Documento_Cliente',
        'Documento_Empleado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Documento_Cliente', 'Documento_Cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Documento_Empleado', 'Documento_Empleado');
    }
}
