<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN PARA TU BASE DE DATOS ---
    protected $table = 'JC_Repuestos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'stock',
    ];

    // --- RELACIONES ELOQUENT ---

    /**
     * Un repuesto puede estar en muchas órdenes de trabajo (relación muchos a muchos).
     */
    public function ordenesTrabajo()
    {
        return $this->belongsToMany(OrdenTrabajo::class, 'JC_Detalle_Orden', 'repuesto_id', 'orden_id')
                    ->withPivot('cantidad', 'precio_unitario'); // Para acceder a los datos de la tabla pivote
    }
}