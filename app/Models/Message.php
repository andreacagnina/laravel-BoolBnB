<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importa il trait SoftDeletes

class Message extends Model
{
    use HasFactory, SoftDeletes; // Usa il trait SoftDeletes

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'message',
        'property_id',
    ];

    // Definisci la colonna per le soft delete
    protected $dates = ['deleted_at'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
