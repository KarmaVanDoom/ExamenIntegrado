--  Creación y uso de la Base de Datos
DROP DATABASE IF EXISTS taller_mecanico_db; -- 
CREATE DATABASE taller_mecanico_db;
USE taller_mecanico_db;

-- =================================================================================================
-- CREACIÓN DE TABLAS
-- =================================================================================================

-- Tabla para gestionar los contadores de las claves primarias (reemplaza AUTO_INCREMENT)
CREATE TABLE JC_Contadores (
    tabla_nombre VARCHAR(50) PRIMARY KEY,
    ultimo_id INT NOT NULL DEFAULT 0
);

-- Tabla de Clientes
CREATE TABLE JC_Clientes (
    id INT PRIMARY KEY,
    run VARCHAR(12) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    -- [CORREGIDO] Se agrega validación para formato de RUN chileno (con o sin puntos, con guion).
    CONSTRAINT chk_run_formato CHECK (run REGEXP '^[0-9]{1,2}\\.?[0-9]{3}\\.?[0-9]{3}-[0-9Kk]$'),
    -- [CORREGIDO] Se cambia la validación de correo a un formato genérico, más práctico para un sistema real.
    CONSTRAINT chk_correo_formato CHECK (correo REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$')
);

-- Tabla de Vehículos
CREATE TABLE JC_Vehiculos (
    id INT PRIMARY KEY,
    patente VARCHAR(10) NOT NULL UNIQUE,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    año YEAR NOT NULL,
    -- [CORREGIDO] Se normalizan los valores del ENUM para no tener espacios.
    tipo ENUM('sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep') NOT NULL,
    cliente_id INT NOT NULL,
    -- [CORREGIDO] Expresión regular actualizada para patentes chilenas (formato antiguo y nuevo).
    -- Formato nuevo: BBBB-12 o BBBB12 (con letras válidas B,C,D,F,G,H,J,K,L,M,N,P,R,S,T,V,W,X,Y,Z).
    -- Formato antiguo: BB-1234 o BB1234.
    CONSTRAINT chk_patente_formato CHECK (patente REGEXP '^[BCDFGHJKLMNPQRSTVWXYZ]{4}[-.]?[0-9]{2}$|^[A-Z]{2}[-.]?[0-9]{4}$'),
    FOREIGN KEY (cliente_id) REFERENCES JC_Clientes(id) ON DELETE CASCADE
);

-- Tabla de Repuestos
CREATE TABLE JC_Repuestos (
    id INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(100),
    precio DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL,
    CONSTRAINT chk_precio CHECK (precio > 0),
    CONSTRAINT chk_stock CHECK (stock >= 0)
);

-- Tabla de Órdenes de Trabajo
CREATE TABLE JC_Ordenes_Trabajo (
    id INT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en proceso', 'finalizada') NOT NULL DEFAULT 'pendiente',
    monto_total DECIMAL(12, 2) DEFAULT 0.00,
    cliente_id INT NOT NULL,
    vehiculo_id INT NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES JC_Clientes(id),
    FOREIGN KEY (vehiculo_id) REFERENCES JC_Vehiculos(id)
);

-- Tabla de Detalle de Órdenes (relación muchos a muchos entre Órdenes y Repuestos)
CREATE TABLE JC_Detalle_Orden (
    id INT PRIMARY KEY,
    orden_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (orden_id) REFERENCES JC_Ordenes_Trabajo(id) ON DELETE CASCADE,
    FOREIGN KEY (repuesto_id) REFERENCES JC_Repuestos(id),
    CONSTRAINT chk_cantidad CHECK (cantidad > 0)
);

-- Inicializar los contadores para las tablas principales
INSERT INTO JC_Contadores (tabla_nombre, ultimo_id) VALUES
('JC_Clientes', 0),
('JC_Vehiculos', 0),
('JC_Repuestos', 0),
('JC_Ordenes_Trabajo', 0),
('JC_Detalle_Orden', 0);

-- =================================================================================================
-- TRIGGERS
-- =================================================================================================

-- Trigger para generar PK de Clientes
DELIMITER $$
CREATE TRIGGER JC_TRG_Before_Insert_Cliente
BEFORE INSERT ON JC_Clientes
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    UPDATE JC_Contadores SET ultimo_id = ultimo_id + 1 WHERE tabla_nombre = 'JC_Clientes';
    SELECT ultimo_id INTO next_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Clientes';
    SET NEW.id = next_id;
END$$
DELIMITER ;

-- Trigger para generar PK de Vehículos
DELIMITER $$
CREATE TRIGGER JC_TRG_Before_Insert_Vehiculo
BEFORE INSERT ON JC_Vehiculos
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    UPDATE JC_Contadores SET ultimo_id = ultimo_id + 1 WHERE tabla_nombre = 'JC_Vehiculos';
    SELECT ultimo_id INTO next_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Vehiculos';
    SET NEW.id = next_id;
END$$
DELIMITER ;

-- Trigger para generar PK de Repuestos
DELIMITER $$
CREATE TRIGGER JC_TRG_Before_Insert_Repuesto
BEFORE INSERT ON JC_Repuestos
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    UPDATE JC_Contadores SET ultimo_id = ultimo_id + 1 WHERE tabla_nombre = 'JC_Repuestos';
    SELECT ultimo_id INTO next_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Repuestos';
    SET NEW.id = next_id;
END$$
DELIMITER ;

-- Trigger para generar PK de Órdenes de Trabajo
DELIMITER $$
CREATE TRIGGER JC_TRG_Before_Insert_Orden
BEFORE INSERT ON JC_Ordenes_Trabajo
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    UPDATE JC_Contadores SET ultimo_id = ultimo_id + 1 WHERE tabla_nombre = 'JC_Ordenes_Trabajo';
    SELECT ultimo_id INTO next_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Ordenes_Trabajo';
    SET NEW.id = next_id;
END$$
DELIMITER ;

-- Trigger para generar PK de Detalle de Orden
DELIMITER $$
CREATE TRIGGER JC_TRG_Before_Insert_Detalle_Orden
BEFORE INSERT ON JC_Detalle_Orden
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    UPDATE JC_Contadores SET ultimo_id = ultimo_id + 1 WHERE tabla_nombre = 'JC_Detalle_Orden';
    SELECT ultimo_id INTO next_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Detalle_Orden';
    SET NEW.id = next_id;
END$$
DELIMITER ;


-- Trigger para reducir el stock de repuestos automáticamente
DELIMITER $$
CREATE TRIGGER JC_TRG_After_Insert_Detalle_Stock
AFTER INSERT ON JC_Detalle_Orden
FOR EACH ROW
BEGIN
    UPDATE JC_Repuestos
    SET stock = stock - NEW.cantidad
    WHERE id = NEW.repuesto_id;
END$$
DELIMITER ;


-- =================================================================================================
-- FUNCIONES
-- =================================================================================================

-- Función para generar el correo electrónico del cliente
DELIMITER $$
CREATE FUNCTION JC_FN_GenerarCorreo(
    p_nombres VARCHAR(100),
    p_apellidos VARCHAR(100)
)
RETURNS VARCHAR(150)
DETERMINISTIC
BEGIN
    DECLARE base_correo VARCHAR(100);
    DECLARE correo_final VARCHAR(150);
    DECLARE contador INT DEFAULT 0;
    DECLARE primer_nombre VARCHAR(50);
    DECLARE primer_apellido VARCHAR(50);
    DECLARE dominio VARCHAR(50) DEFAULT '@rapidoyfurioso.cl'; -- Dominio por defecto

    SET primer_nombre = SUBSTRING_INDEX(LOWER(p_nombres), ' ', 1);
    SET primer_apellido = SUBSTRING_INDEX(LOWER(p_apellidos), ' ', 1);
    SET base_correo = CONCAT(REPLACE(primer_nombre, ' ', ''), '.', REPLACE(primer_apellido, ' ', ''));
    SET correo_final = CONCAT(base_correo, dominio);

    WHILE (SELECT COUNT(*) FROM JC_Clientes WHERE correo = correo_final) > 0 DO
        SET contador = contador + 1;
        SET correo_final = CONCAT(base_correo, '.', contador, dominio);
    END WHILE;

    RETURN correo_final;
END$$
DELIMITER ;


-- Función para calcular el total de una orden de trabajo
DELIMITER $$
CREATE FUNCTION JC_FN_CalcularTotalOrden(
    p_orden_id INT
)
RETURNS DECIMAL(12, 2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE total DECIMAL(12, 2);

    SELECT SUM(cantidad * precio_unitario)
    INTO total
    FROM JC_Detalle_Orden
    WHERE orden_id = p_orden_id;

    RETURN IFNULL(total, 0.00);
END$$
DELIMITER ;


-- =================================================================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =================================================================================================

-- --- Procedimientos para Clientes ---

DELIMITER $$
CREATE PROCEDURE JC_InsertarCliente(
    IN p_run VARCHAR(12),
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255)
)
BEGIN
    DECLARE v_correo VARCHAR(150);
    SET v_correo = JC_FN_GenerarCorreo(p_nombres, p_apellidos);
    INSERT INTO JC_Clientes (run, nombres, apellidos, telefono, correo, direccion)
    VALUES (p_run, p_nombres, p_apellidos, p_telefono, v_correo, p_direccion);
    SELECT * FROM JC_Clientes WHERE correo = v_correo;
END$$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE JC_ActualizarCliente(
    IN p_id INT,
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(255)
)
BEGIN
    UPDATE JC_Clientes
    SET nombres = p_nombres,
        apellidos = p_apellidos,
        telefono = p_telefono,
        direccion = p_direccion
    WHERE id = p_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_EliminarCliente(
    IN p_id INT
)
BEGIN
    DELETE FROM JC_Clientes WHERE id = p_id;
END$$
DELIMITER ;


-- --- Procedimientos para Vehículos ---

DELIMITER $$
CREATE PROCEDURE JC_InsertarVehiculo(
    IN p_patente VARCHAR(10),
    IN p_marca VARCHAR(50),
    IN p_modelo VARCHAR(50),
    IN p_año YEAR,
    -- [CORREGIDO] Se actualiza el ENUM para coincidir con la definición de la tabla.
    IN p_tipo ENUM('sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep'),
    IN p_cliente_id INT
)
BEGIN
    INSERT INTO JC_Vehiculos (patente, marca, modelo, año, tipo, cliente_id)
    VALUES (UPPER(p_patente), p_marca, p_modelo, p_año, p_tipo, p_cliente_id);
    SELECT * FROM JC_Vehiculos WHERE patente = UPPER(p_patente);
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_ActualizarVehiculo(
    IN p_id INT,
    IN p_marca VARCHAR(50),
    IN p_modelo VARCHAR(50),
    IN p_año YEAR,
    -- [CORREGIDO] Se actualiza el ENUM para coincidir con la definición de la tabla.
    IN p_tipo ENUM('sedan', 'hatchback', 'suv', 'station_wagon', 'pickup', 'jeep')
)
BEGIN
    UPDATE JC_Vehiculos
    SET marca = p_marca,
        modelo = p_modelo,
        año = p_año,
        tipo = p_tipo
    WHERE id = p_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_EliminarVehiculo(
    IN p_id INT
)
BEGIN
    DELETE FROM JC_Vehiculos WHERE id = p_id;
END$$
DELIMITER ;


-- --- Procedimientos para Repuestos ---

DELIMITER $$
CREATE PROCEDURE JC_InsertarRepuesto(
    IN p_nombre VARCHAR(100),
    IN p_categoria VARCHAR(100),
    IN p_precio DECIMAL(10, 2),
    IN p_stock INT
)
BEGIN
    INSERT INTO JC_Repuestos (nombre, categoria, precio, stock)
    VALUES (p_nombre, p_categoria, p_precio, p_stock);
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_ActualizarRepuesto(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_categoria VARCHAR(100),
    IN p_precio DECIMAL(10, 2),
    IN p_stock INT
)
BEGIN
    UPDATE JC_Repuestos
    SET nombre = p_nombre,
        categoria = p_categoria,
        precio = p_precio,
        stock = p_stock
    WHERE id = p_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_EliminarRepuesto(
    IN p_id INT
)
BEGIN
    DELETE FROM JC_Repuestos WHERE id = p_id;
END$$
DELIMITER ;


-- --- Procedimientos para Órdenes de Trabajo ---

DELIMITER $$
CREATE PROCEDURE JC_CrearOrdenTrabajo(
    IN p_cliente_id INT,
    IN p_vehiculo_id INT
)
BEGIN
    INSERT INTO JC_Ordenes_Trabajo (cliente_id, vehiculo_id, fecha_hora)
    VALUES (p_cliente_id, p_vehiculo_id, NOW());
    SELECT * FROM JC_Ordenes_Trabajo WHERE id = (SELECT ultimo_id FROM JC_Contadores WHERE tabla_nombre = 'JC_Ordenes_Trabajo');
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_AgregarDetalleOrden(
    IN p_orden_id INT,
    IN p_repuesto_id INT,
    IN p_cantidad INT
)
BEGIN
    DECLARE v_precio DECIMAL(10, 2);
    -- Capturar el precio actual del repuesto
    SELECT precio INTO v_precio FROM JC_Repuestos WHERE id = p_repuesto_id;
    
    INSERT INTO JC_Detalle_Orden (orden_id, repuesto_id, cantidad, precio_unitario)
    VALUES (p_orden_id, p_repuesto_id, p_cantidad, v_precio);
    
    -- Actualizar el monto total en la orden
    UPDATE JC_Ordenes_Trabajo
    SET monto_total = JC_FN_CalcularTotalOrden(p_orden_id)
    WHERE id = p_orden_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE JC_ActualizarEstadoOrden(
    IN p_orden_id INT,
    IN p_estado ENUM('pendiente', 'en proceso', 'finalizada')
)
BEGIN
    UPDATE JC_Ordenes_Trabajo
    SET estado = p_estado
    WHERE id = p_orden_id;
END$$
DELIMITER ;

-- =================================================================================================
-- SCRIPT FINALIZADO
-- =================================================================================================