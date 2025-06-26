<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'qr_path',
        'qr_token',
        'qr_activo'
    ];

    protected $casts = [
        'qr_activo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cliente) {
            if (!$cliente->qr_token) {
                $cliente->qr_token = Str::uuid();
            }
        });
    }

    public function getNombreCompletoAttribute()
    {
        $nombre = $this->nombre;
        $apellido_paterno = $this->apellido_paterno ?? '';
        $apellido_materno = $this->apellido_materno ?? '';

        return trim("{$nombre} {$apellido_paterno} {$apellido_materno}");
    }

    public function getQrUrlAttribute()
    {
        return route('cliente.saludo', ['token' => $this->qr_token]);
    }
}
