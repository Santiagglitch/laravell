<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    // Nombre real de la tabla
    protected $table = 'clientes';

    // Clave primaria
    protected $primaryKey = 'Documento_Cliente';

    // La PK es VARCHAR, no autoincrementa
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'Documento_Cliente',
        'Nombre_Cliente',
        'Apellido_Cliente',
        'Telefono',
        'Fecha_Nacimiento',
        'Genero',
        'ID_Estado'
    ];
}
