<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'JC_Clientes';

    /**
     * La clave primaria para el modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la clave primaria es autoincremental.
     * Se establece en false porque usamos un trigger para generar el ID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe tener timestamps (created_at, updated_at).
     * Se establece en false porque la tabla no los tiene.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'run',
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'direccion',
    ];
}