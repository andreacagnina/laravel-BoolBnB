<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'description',
        'num_rooms',
        'num_beds',
        'num_baths',
        'mq',
        'address',
        'lat',
        'long',
        'price',
        'type',
        'floor',
        'available',
        'sponsored',
        'user_id'
    ];

    public static function generateSlug($name)
    {
        return Str::slug($name, '-');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class);
    }
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
    public function views()
    {
        return $this->hasMany(View::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
