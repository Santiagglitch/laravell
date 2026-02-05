<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'Estados';
    protected $primaryKey = 'ID_Estado';

    public $timestamps = false;

    protected $fillable = [
        'Nombre_Estado',
    ];
}
