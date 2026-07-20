<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'country_code', 'currency', 'region', 'language', 'gdp', 'inflation', 'population'];

    public function ports()
    {
        return $this->hasMany(Port::class);
    }
}
