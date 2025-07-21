<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

{
    public function up(): void
    {
        // Tabla JC_Contadores
        Schema::create('JC_Contadores', function (Blueprint $table) {
            $table->string('tabla_nombre', 50)->primary();
            $table->integer('ultimo_id')->default(0);
        });

        // Tabla JC_Clientes
        Schema::create('JC_Clientes', function (Blueprint $table) {
            $table->id();
            $table->string('run', 12)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 15);
            $table->string('correo', 150)->unique();
            $table->string('direccion', 255)->nullable();
            $table->check("run REGEXP '^[0-9]{1,2}\\.?[0-9]{3}\\.?[0-9]{3}-[0-9Kk]$'");
            $table->check("correo REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'");
        });

        // Tabla JC_Vehiculos
        Schema::create('JC_Vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('patente', 10)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->year('año');
            $table->enum('tipo', ['sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep']);
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('JC_Clientes')->onDelete('cascade');
            $table->check("patente REGEXP '^[BCDFGHJKLMNPQRSTVWXYZ]{4}[-.]?[0-9]{2}$|^[A-Z]{2}[-.]?[0-9]{4}$'");
        });

        // Tabla JC_Repuestos
        Schema::create('JC_Repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('categoria', 100)->nullable();
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock');
            $table->check('precio > 0');
            $table->check('stock >= 0');
        });

        // Tabla JC_Ordenes_Trabajo
        Schema::create('JC_Ordenes_Trabajo', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado', ['pendiente', 'en proceso', 'finalizada'])->default('pendiente');
            $table->decimal('monto_total', 12, 2)->default(0.00);
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->foreign('cliente_id')->references('id')->on('JC_Clientes');
            $table->foreign('vehiculo_id')->references('id')->on('JC_Vehiculos');
        });

        // Tabla JC_Detalle_Orden
        Schema::create('JC_Detalle_Orden', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('repuesto_id');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->foreign('orden_id')->references('id')->on('JC_Ordenes_Trabajo')->onDelete('cascade');
            $table->foreign('repuesto_id')->references('id')->on('JC_Repuestos');
            $table->check('cantidad > 0');
        });

        // Insert inicial a JC_Contadores
        DB::table('JC_Contadores')->insert([
            ['tabla_nombre' => 'JC_Clientes', 'ultimo_id' => 0],
            ['tabla_nombre' => 'JC_Vehiculos', 'ultimo_id' => 0],
            ['tabla_nombre' => 'JC_Repuestos', 'ultimo_id' => 0],
            ['tabla_nombre' => 'JC_Ordenes_Trabajo', 'ultimo_id' => 0],
            ['tabla_nombre' => 'JC_Detalle_Orden', 'ultimo_id' => 0],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('JC_Detalle_Orden');
        Schema::dropIfExists('JC_Ordenes_Trabajo');
        Schema::dropIfExists('JC_Repuestos');
        Schema::dropIfExists('JC_Vehiculos');
        Schema::dropIfExists('JC_Clientes');
        Schema::dropIfExists('JC_Contadores');
    }
};
