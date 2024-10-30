<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration',
    ];

    public static function generateSlug($name)
    {
        return Str::slug($name, '-');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }
}
