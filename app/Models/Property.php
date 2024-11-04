<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    public function checkSponsorshipStatus()
    {
        $now = Carbon::now();

        // Verifica se esiste almeno uno sponsor con una `end_date` futura
        $hasActiveSponsor = $this->sponsors()
            ->wherePivot('end_date', '>', $now)
            ->exists();

        // Aggiorna lo stato `sponsored` in base alla presenza di sponsor attivi
        $this->sponsored = $hasActiveSponsor;
        $this->save();
    }


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
        return $this->belongsToMany(Sponsor::class)
            ->withTimestamps();
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
