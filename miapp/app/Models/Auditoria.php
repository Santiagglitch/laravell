<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'Auditoria';
    protected $primaryKey = 'ID_Auditoria';
    public $timestamps = false;

    protected $guarded = [];
}
