<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    // ✅ TABLA REAL en tu BD (según tu script)
    protected $table = 'Proveedores';

    // ✅ PK REAL
    protected $primaryKey = 'ID_Proveedor';

    // ✅ En tu BD es INT AUTO_INCREMENT, no string
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'Nombre_Proveedor',
        'Correo_Electronico',
        'Telefono',
        'ID_Estado'
    ];

    // (Opcional pero útil) para mostrar nombre de estado igual que empleados
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'ID_Estado', 'ID_Estado');
    }
}
