<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    // Nombre real de la tabla
    protected $table = 'ventas';

    // Clave primaria
    protected $primaryKey = 'ID_Venta';

    // La PK es VARCHAR, no autoincrementa
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'ID_Venta',
        'Documento_Cliente',
        'Documento_Empleado'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Documento_Cliente', 'Documento_Cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'Documento_Empleado', 'Documento_Empleado');
    }
    
}
