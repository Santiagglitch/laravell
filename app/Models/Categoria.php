<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'Categorias';
    protected $primaryKey = 'ID_Categoria';
    public $timestamps = false;

    protected $fillable = ['Nombre_Categoria'];
}
