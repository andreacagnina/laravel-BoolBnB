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

    // Append custom attributes to the model's array form
    protected $appends = ['cover_image_url'];

    // Cast price to float to ensure numeric type
    protected $casts = [
        'price' => 'float',
    ];

    // Accessor for the cover image URL
    public function getCoverImageUrlAttribute()
    {
        if (Str::startsWith($this->cover_image, 'http')) {
            return $this->cover_image;
        }
        return asset('storage/' . $this->cover_image);
    }

    public static function generateSlug($name)
    {
        return Str::slug($name, '-');
    }

    public function checkSponsorshipStatus()
    {
        $now = Carbon::now();

        // Check if there's at least one sponsor with a future end_date
        $hasActiveSponsor = $this->sponsors()
            ->wherePivot('end_date', '>', $now)
            ->exists();

        // Update the sponsored status based on active sponsors
        $this->sponsored = $hasActiveSponsor;
        $this->save();
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class)->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
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
