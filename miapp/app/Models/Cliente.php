<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    
    protected $primaryKey = 'Documento_Cliente';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'Documento_Cliente',
        'Nombre_Cliente',
        'Apellido_Cliente',
        'ID_Estado'
    ];
}