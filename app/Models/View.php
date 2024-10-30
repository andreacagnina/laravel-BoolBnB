<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'property_id',
    ];
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
