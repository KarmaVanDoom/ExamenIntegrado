<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN PARA TU BASE DE DATOS ---
    protected $table = 'JC_Vehiculos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'patente',
        'marca',
        'modelo',
        'año',
        'tipo',
        'cliente_id',
    ];

    // --- RELACIONES ELOQUENT ---

    /**
     * Un vehículo pertenece a un solo cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Un vehículo puede tener muchas órdenes de trabajo.
     */
    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'vehiculo_id');
    }
}