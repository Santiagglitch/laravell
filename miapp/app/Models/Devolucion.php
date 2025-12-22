<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $primaryKey = 'ID_Devolucion';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ID_Devolucion',
        'Fecha_Devolucion',
        'Motivo'
    ];
}
