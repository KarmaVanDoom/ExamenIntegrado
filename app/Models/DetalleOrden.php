<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN PARA TU BASE DE DATOS ---
    protected $table = 'JC_Detalle_Orden';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'orden_id',
        'repuesto_id',
        'cantidad',
        'precio_unitario',
    ];

    /**
     * Convierte el precio a un tipo decimal.
     */
    protected $casts = [
        'precio_unitario' => 'decimal:2',
    ];

    // --- RELACIONES ELOQUENT ---

    /**
     * Un detalle pertenece a una orden de trabajo.
     */
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_id');
    }

    /**
     * Un detalle se refiere a un repuesto.
     */
    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class, 'repuesto_id');
    }
}