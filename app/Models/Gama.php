<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gama extends Model
{
    protected $table = 'Gamas';
    protected $primaryKey = 'ID_Gama';
    public $timestamps = false;

    protected $fillable = ['Nombre_Gama'];
}
