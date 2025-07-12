<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN PARA TU BASE DE DATOS ---
    protected $table = 'JC_Ordenes_Trabajo';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'fecha_hora',
        'estado',
        'monto_total',
        'cliente_id',
        'vehiculo_id',
    ];

    /**
     * Convierte automáticamente ciertos atributos a tipos de datos nativos.
     */
    protected $casts = [
        'fecha_hora' => 'datetime', // Trata la columna como un objeto Carbon (para fechas)
        'monto_total' => 'decimal:2',
    ];

    // --- RELACIONES ELOQUENT ---

    /**
     * Una orden de trabajo pertenece a un cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Una orden de trabajo pertenece a un vehículo.
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    /**
     * Una orden de trabajo puede tener muchos repuestos (relación muchos a muchos).
     */
    public function repuestos()
    {
        return $this->belongsToMany(Repuesto::class, 'JC_Detalle_Orden', 'orden_id', 'repuesto_id')
                    ->withPivot('cantidad', 'precio_unitario'); // Para acceder a los datos de la tabla pivote
    }

    /**
     * Una orden de trabajo tiene muchos detalles.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_id');
    }
}