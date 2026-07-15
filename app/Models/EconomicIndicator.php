<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicIndicator extends Model
{
    // Pastikan nama tabelnya sesuai dengan yang ada di database kamu
    protected $table = 'economic_indicators'; 

    // Daftar kolom yang boleh diisi
    protected $fillable = ['indicator_name', 'value', 'created_at'];
}