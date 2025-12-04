<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    // Nombre real de la tabla
    protected $table = 'devoluciones';

    // Clave primaria
    protected $primaryKey = 'ID_Devolucion';

    // La PK es VARCHAR, no autoincrementa
    public $incrementing = false;
    protected $keyType = 'string';

    // Tu tabla no tiene created_at / updated_at
    public $timestamps = false;

    // Campos rellenables
    protected $fillable = [
        'ID_Devolucion',
        'Fecha_Devolucion',
        'Motivo'
    ];    
}
