<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;
    protected $guarded =[];
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'radius',
    ];

    protected $casts = [
        'location' => 'array',
    ];

    public function setLocationAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['location'] = json_encode($value);
        } else {
            $this->attributes['location'] = $value;
        }
    }

    public function getLocationAttribute($value)
    {
        return json_decode($value, true);
    }

}
