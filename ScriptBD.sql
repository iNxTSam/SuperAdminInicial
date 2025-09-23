-- Base de Datos: Sistema de Gestión de Parqueadero
-- Edificio SENA QUIRIGUA CEET

CREATE DATABASE IF NOT EXISTS proyecto_parqueadero;
USE proyecto_parqueadero;

-- Tabla de Roles de Usuario
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    carnet VARCHAR(20),
    contacto VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de Tipos de Vehículo
CREATE TABLE tipos_vehiculo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Vehículos
CREATE TABLE vehiculos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    placa VARCHAR(10) NOT NULL UNIQUE,
    tipo_vehiculo_id INT NOT NULL,
    propietario_id INT NOT NULL,
    autorizado_por_id INT,
    color VARCHAR(30),
    marca VARCHAR(50),
    modelo VARCHAR(50),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id),
    FOREIGN KEY (propietario_id) REFERENCES usuarios(id),
    FOREIGN KEY (autorizado_por_id) REFERENCES usuarios(id)
);

-- Tabla de Bahías/Espacios de Parqueo
CREATE TABLE bahias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero VARCHAR(10) NOT NULL UNIQUE,
    tipo_vehiculo_id INT NOT NULL,
    capacidad_maxima INT DEFAULT 1,
    ocupada BOOLEAN DEFAULT FALSE,
    activa BOOLEAN DEFAULT TRUE,
    ubicacion VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id)
);

-- Tabla de Tarifas
CREATE TABLE tarifas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    tipo VARCHAR(20) NOT NULL, -- minima, diurna, nocturna, 24h, semanal, mensual, sena, sena_cesante
    tipo_vehiculo_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id)
);

-- Tabla de Estados de Contrato
CREATE TABLE estados_contrato (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Contratos
CREATE TABLE contratos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehiculo_id INT NOT NULL,
    tarifa_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado_id INT NOT NULL,
    valor_total DECIMAL(10,2),
    observaciones TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id),
    FOREIGN KEY (tarifa_id) REFERENCES tarifas(id),
    FOREIGN KEY (estado_id) REFERENCES estados_contrato(id),
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
);

-- Tabla de Recibos (numeración automática desde 1001)
CREATE TABLE recibos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero VARCHAR(20) NOT NULL UNIQUE,
    contrato_id INT NOT NULL,
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valor DECIMAL(10,2) NOT NULL,
    concepto TEXT,
    estado VARCHAR(20) DEFAULT 'pendiente', -- pendiente, pagado, anulado
    emitido_por INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contrato_id) REFERENCES contratos(id),
    FOREIGN KEY (emitido_por) REFERENCES usuarios(id)
);

-- Tabla de Tickets de Entrada/Salida
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_ticket VARCHAR(20) NOT NULL UNIQUE,
    vehiculo_id INT NOT NULL,
    bahia_id INT NOT NULL,
    fecha_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_salida TIMESTAMP NULL,
    valor_pagado DECIMAL(10,2) DEFAULT 0,
    estado VARCHAR(20) DEFAULT 'activo', -- activo, finalizado, anulado
    registrado_por INT NOT NULL,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id),
    FOREIGN KEY (bahia_id) REFERENCES bahias(id),
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

-- Tabla de Alertas
CREATE TABLE alertas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(30) NOT NULL, -- contrato_vencido, carnet_vencido, bahia_sobrecupo
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    entidad_tipo VARCHAR(30), -- contrato, usuario, bahia
    entidad_id INT,
    leida BOOLEAN DEFAULT FALSE,
    fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de Reportes Generados
CREATE TABLE reportes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(30) NOT NULL, -- vehiculos, usuarios, contratos, ingresos
    formato VARCHAR(10) NOT NULL, -- CSV, PDF
    ruta_archivo VARCHAR(255),
    parametros JSON,
    generado_por INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generado_por) REFERENCES usuarios(id)
);

-- Insertar datos iniciales

-- Roles del sistema
INSERT INTO roles (nombre, descripcion) VALUES 
('Super Administrador', 'Configuración global, tarifas, reportes y cupos'),
('Administrador', 'Manejo de usuarios, contratos y cobros'),
('Vigilante', 'Registro de entradas/salidas y emisión de tickets');

-- Estados de contrato
INSERT INTO estados_contrato (nombre, descripcion) VALUES 
('activo', 'Contrato vigente'),
('vencido', 'Contrato que ha superado su fecha de fin'),
('cancelado', 'Contrato cancelado antes de tiempo'),
('finalizado', 'Contrato completado normalmente');

-- Tipos de vehículo
INSERT INTO tipos_vehiculo (nombre, descripcion) VALUES 
('Automóvil', 'Vehículos de 4 ruedas tipo sedan, hatchback, SUV'),
('Motocicleta', 'Vehículos de 2 ruedas'),
('Bicicleta', 'Vehículos no motorizados de 2 ruedas');

-- Tarifas ejemplo
INSERT INTO tarifas (nombre, tipo, tipo_vehiculo_id, valor) VALUES 
('Tarifa Mínima Auto', 'minima', 1, 2000.00),
('Tarifa Diurna Auto', 'diurna', 1, 5000.00),
('Tarifa Nocturna Auto', 'nocturna', 1, 8000.00),
('Tarifa 24h Auto', '24h', 1, 15000.00),
('Tarifa Mensual Auto', 'mensual', 1, 300000.00),
('Tarifa SENA Auto', 'sena', 1, 200000.00),
('Tarifa Mínima Moto', 'minima', 2, 1000.00),
('Tarifa Diurna Moto', 'diurna', 2, 2500.00),
('Tarifa Mensual Moto', 'mensual', 2, 150000.00),
('Tarifa SENA Moto', 'sena', 2, 100000.00);

-- Bahías ejemplo
INSERT INTO bahias (numero, tipo_vehiculo_id, ubicacion) VALUES 
('A001', 1, 'Piso 1 - Zona A'),
('A002', 1, 'Piso 1 - Zona A'),
('A003', 1, 'Piso 1 - Zona A'),
('B001', 2, 'Piso 1 - Zona Motos'),
('B002', 2, 'Piso 1 - Zona Motos'),
('B003', 2, 'Piso 1 - Zona Motos'),
('C001', 3, 'Zona Bicicletas'),
('C002', 3, 'Zona Bicicletas');

-- Usuario administrador inicial
INSERT INTO usuarios (nombre, cedula, email, password, rol_id) VALUES 
('Administrador Sistema', '12345678', 'admin@sena.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Índices para optimización
CREATE INDEX idx_vehiculos_placa ON vehiculos(placa);
CREATE INDEX idx_contratos_fechas ON contratos(fecha_inicio, fecha_fin);
CREATE INDEX idx_tickets_fecha_entrada ON tickets(fecha_entrada);
CREATE INDEX idx_recibos_numero ON recibos(numero);
CREATE INDEX idx_alertas_fecha ON alertas(fecha_alerta);
CREATE INDEX idx_usuarios_cedula ON usuarios(cedula);

-- Trigger para numeración automática de recibos desde 1001
DELIMITER $$
CREATE TRIGGER tr_recibos_numero 
BEFORE INSERT ON recibos
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero, 2) AS UNSIGNED)), 1000) + 1 INTO next_number FROM recibos WHERE numero REGEXP '^R[0-9]+$';
    SET NEW.numero = CONCAT('R', next_number);
END$$

-- Trigger para actualizar estado de bahías al registrar entrada
CREATE TRIGGER tr_tickets_entrada 
AFTER INSERT ON tickets
FOR EACH ROW
BEGIN
    UPDATE bahias SET ocupada = TRUE WHERE id = NEW.bahia_id;
END$$

-- Trigger para liberar bahías al registrar salida
CREATE TRIGGER tr_tickets_salida 
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF NEW.fecha_salida IS NOT NULL AND OLD.fecha_salida IS NULL THEN
        UPDATE bahias SET ocupada = FALSE WHERE id = NEW.bahia_id;
    END IF;
END$$

DELIMITER ;

-- Vista para reportes de ocupación
CREATE VIEW v_ocupacion_bahias AS
SELECT 
    b.numero as bahia,
    b.ubicacion,
    tv.nombre as tipo_vehiculo,
    b.ocupada,
    v.placa,
    u.nombre as propietario,
    t.fecha_entrada
FROM bahias b
LEFT JOIN tipos_vehiculo tv ON b.tipo_vehiculo_id = tv.id
LEFT JOIN tickets t ON b.id = t.bahia_id AND t.estado = 'activo'
LEFT JOIN vehiculos v ON t.vehiculo_id = v.id
LEFT JOIN usuarios u ON v.propietario_id = u.id;

-- Vista para contratos próximos a vencer
CREATE VIEW v_contratos_por_vencer AS
SELECT 
    c.id,
    u.nombre as propietario,
    v.placa,
    c.fecha_fin,
    DATEDIFF(c.fecha_fin, CURDATE()) as dias_restantes,
    t.nombre as tarifa,
    e.nombre as estado
FROM contratos c
JOIN vehiculos v ON c.vehiculo_id = v.id
JOIN usuarios u ON v.propietario_id = u.id
JOIN tarifas t ON c.tarifa_id = t.id
JOIN estados_contrato e ON c.estado_id = e.id
WHERE c.fecha_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
AND e.nombre = 'activo';

SELECT 'Base de datos creada exitosamente' as mensaje;
