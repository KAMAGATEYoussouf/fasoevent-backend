<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Event extends Model
{
    use HasFactory, HasApiTokens, HasUuids;

    protected $primaryKey = 'id'; // Définit l'ID comme clé primaire
    protected $keyType = 'string'; // Type de clé primaire : string (pour UUID)
    public $incrementing = false; // Désactive l'incrémentation automatique

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'price',
        'image',
        'is_active',
        'city_id',
    ];

    /**
     * Relation avec la ville.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Relation many-to-many avec les utilisateurs (réservations).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id')->withTimestamps();
    }
}
