<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Naissance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_lapin',
        'sexe',
        'date_naissance',
        'poids'
    ];
}
