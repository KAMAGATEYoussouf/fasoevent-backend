<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class City extends Model
{
    use HasFactory, HasApiTokens, HasUuids;

    protected $primaryKey = 'id'; // Définit l'ID comme clé primaire
    protected $keyType = 'string'; // Type de clé primaire : string (pour UUID)
    public $incrementing = false; // Désactive l'incrémentation automatique

    protected $fillable = [
        'name', // Champs remplissables
    ];
}
