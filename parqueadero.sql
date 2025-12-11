-- ================================================
--   CREACIÓN DE BASE DE DATOS
-- ================================================
DROP DATABASE IF EXISTS proyecto_parqueadero;
CREATE DATABASE proyecto_parqueadero
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE proyecto_parqueadero;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ================================================
--   TABLAS BASE (SIN DEPENDENCIAS)
-- ================================================

CREATE TABLE roles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE rolclientes (
  idRol INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(15) NOT NULL,
  descripcion VARCHAR(100) DEFAULT NULL
);

CREATE TABLE estados_contrato (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(30) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tipos_vehiculo (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tiempocontrato (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(30) NOT NULL,
  dias INT NOT NULL
);

-- ================================================
--   TABLAS MAESTRAS RELACIONADAS
-- ================================================

CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  cedula VARCHAR(20) NOT NULL UNIQUE,
  carnet VARCHAR(20) DEFAULT NULL,
  contacto VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  rol_id INT NOT NULL,
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_usuarios_cedula (cedula),
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);

CREATE TABLE clientes (
  id VARCHAR(15) PRIMARY KEY,
  nombre VARCHAR(70) NOT NULL,
  telefono VARCHAR(20) DEFAULT NULL,
  correo VARCHAR(50) DEFAULT NULL,
  activo TINYINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  rol INT,
  FOREIGN KEY (rol) REFERENCES rolclientes(idRol)
);

CREATE TABLE vehiculos (
  placa VARCHAR(10) PRIMARY KEY,
  tipo_vehiculo_id INT NOT NULL,
  propietario_id INT NOT NULL,
  autorizado_por_id INT DEFAULT NULL,
  color VARCHAR(30),
  marca VARCHAR(50),
  modelo VARCHAR(50),
  estado_parqueo INT DEFAULT 0,
  activo TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_vehiculos_placa (placa),
  FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id),
  FOREIGN KEY (autorizado_por_id) REFERENCES usuarios(id)
);

-- ================================================
--   TABLAS OPERATIVAS
-- ================================================

CREATE TABLE tarifas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  tipo_vehiculo_id INT NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  activa TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id)
);

CREATE TABLE bahias (
  id INT PRIMARY KEY AUTO_INCREMENT,
  numero VARCHAR(10) NOT NULL UNIQUE,
  tipo_vehiculo_id INT NOT NULL,
  capacidad_maxima INT DEFAULT 1,
  ocupada TINYINT(1) DEFAULT 0,
  activa TINYINT(1) DEFAULT 1,
  ubicacion VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo(id)
);

CREATE TABLE contratos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  vehiculo_id VARCHAR(10) NOT NULL,
  tarifa_id INT NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  estado_id INT NOT NULL,
  observaciones TEXT,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  propietario_id VARCHAR(15) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  INDEX idx_contratos_fechas (fecha_inicio, fecha_fin),
  FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(placa),
  FOREIGN KEY (tarifa_id) REFERENCES tarifas(id),
  FOREIGN KEY (estado_id) REFERENCES estados_contrato(id),
  FOREIGN KEY (created_by) REFERENCES usuarios(id),
  FOREIGN KEY (propietario_id) REFERENCES clientes(id)
);

CREATE TABLE tickets_salida (
id INT PRIMARY KEY NOT NULL, 
estado TINYINT(4));

CREATE TABLE entrada_salida (
  id INT PRIMARY KEY AUTO_INCREMENT,
  propietario VARCHAR(15) NOT NULL,
  vehiculo VARCHAR(10) DEFAULT NULL,
  marcar_salida TINYINT(1) DEFAULT 0,
  fecha_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_salida DATETIME DEFAULT NULL,
  ticket_salida INT,
  FOREIGN KEY (propietario) REFERENCES clientes(id),
  FOREIGN KEY (vehiculo) REFERENCES vehiculos(placa),
  FOREIGN KEY (ticket_salida) REFERENCES tickets_salida(id)
);

CREATE TABLE tickets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  numero_ticket VARCHAR(20) NOT NULL UNIQUE,
  vehiculo_id VARCHAR(10) NOT NULL,
  bahia_id INT NOT NULL,
  fecha_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_salida TIMESTAMP NULL DEFAULT NULL,
  valor_pagado DECIMAL(10,2) DEFAULT 0.00,
  estado VARCHAR(20) DEFAULT 'activo',
  registrado_por INT NOT NULL,
  observaciones TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_tickets_fecha_entrada (fecha_entrada),
  FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(placa),
  FOREIGN KEY (bahia_id) REFERENCES bahias(id),
  FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

CREATE TABLE recibos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  numero VARCHAR(20) NOT NULL UNIQUE,
  contrato_id INT NOT NULL,
  fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  valor DECIMAL(10,2) NOT NULL,
  concepto TEXT,
  estado VARCHAR(20) DEFAULT 'pendiente',
  emitido_por INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (contrato_id) REFERENCES contratos(id),
  FOREIGN KEY (emitido_por) REFERENCES usuarios(id)
);

CREATE TABLE alertas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  tipo VARCHAR(30) NOT NULL,
  titulo VARCHAR(100) NOT NULL,
  mensaje TEXT NOT NULL,
  entidad_tipo VARCHAR(30),
  entidad_id INT,
  leida TINYINT(1) DEFAULT 0,
  fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  usuario_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_alertas_fecha (fecha_alerta),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE reportes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  tipo VARCHAR(30) NOT NULL,
  formato VARCHAR(10) NOT NULL,
  ruta_archivo VARCHAR(255),
  parametros LONGTEXT CHECK (JSON_VALID(parametros)),
  generado_por INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (generado_por) REFERENCES usuarios(id)
);

-- ================================================
--   DATOS INICIALES
-- ================================================

INSERT INTO estados_contrato (id,nombre) VALUES
(1, 'Activo'),
(2, 'Cancelado'),
(3, 'Finalizado');

INSERT INTO tipos_vehiculo (id,nombre) VALUES
(5,'Carro'),
(6,'Motocicleta'),
(7,'Bicicleta'),
(8,'Bicicleta electrica');

INSERT INTO rolclientes (idRol,nombre) VALUES
(1,'Aprendiz'),
(2,'Natural');

INSERT INTO roles (id,nombre) VALUES
(1,'Super administrador'),
(2,'Administrador'),
(3,'Vigilante');

INSERT INTO tiempocontrato (id,nombre,dias) VALUES
(1,'Mes',30),
(2,'Quincenal',15),
(3,'Semana',7),
(4,'Dia',1);

INSERT INTO tarifas (id,nombre,tipo,tipo_vehiculo_id,valor) VALUES
(11,'Diurna','Dia',6,15000),
(12,'Tarifa del Mes','Mes',5,68000);

INSERT INTO bahias (id, numero, tipo_vehiculo_id, capacidad_maxima, ocupada, activa, created_at, updated_at)
VALUES
(12,'A001',5,30,0,1,'2025-11-23 01:24:13','2025-12-04 12:17:28'),
(13,'C002',6,30,0,1,'2025-11-23 01:24:24','2025-12-04 01:54:13'),
(14,'C003',7,15,0,1,'2025-11-23 01:24:35','2025-11-23 01:24:35'),
(15,'B001',8,4,0,1,'2025-11-23 01:24:45','2025-12-04 12:17:54');

INSERT INTO usuarios ( nombre,cedula,password,rol_id) VALUES ('PruebaSuperAdministrador', 1031807022,'$2y$12$w65vU/lpre4LoJbJPv0hSu0mTdCTNRyOcVXnUxtiQAcpBPNQbunM6',1);
INSERT INTO usuarios ( nombre,cedula,password,rol_id) VALUES ('PruebaAdministrador', 1031807023,'$2y$12$w65vU/lpre4LoJbJPv0hSu0mTdCTNRyOcVXnUxtiQAcpBPNQbunM6',2);
INSERT INTO usuarios ( nombre,cedula,password,rol_id) VALUES ('PruebaVigilante', 1031807024,'$2y$12$w65vU/lpre4LoJbJPv0hSu0mTdCTNRyOcVXnUxtiQAcpBPNQbunM6',3);

INSERT INTO tickets_salida (id, estado) VALUES (1, 0);
INSERT INTO tickets_salida (id, estado) VALUES (2, 0);
INSERT INTO tickets_salida (id, estado) VALUES (3, 0);
INSERT INTO tickets_salida (id, estado) VALUES (4, 0);
INSERT INTO tickets_salida (id, estado) VALUES (5, 0);
INSERT INTO tickets_salida (id, estado) VALUES (6, 0);
INSERT INTO tickets_salida (id, estado) VALUES (7, 0);
INSERT INTO tickets_salida (id, estado) VALUES (8, 0);
INSERT INTO tickets_salida (id, estado) VALUES (9, 0);
INSERT INTO tickets_salida (id, estado) VALUES (10, 0);
INSERT INTO tickets_salida (id, estado) VALUES (11, 0);
INSERT INTO tickets_salida (id, estado) VALUES (12, 0);
INSERT INTO tickets_salida (id, estado) VALUES (13, 0);
INSERT INTO tickets_salida (id, estado) VALUES (14, 0);
INSERT INTO tickets_salida (id, estado) VALUES (15, 0);
INSERT INTO tickets_salida (id, estado) VALUES (16, 0);
INSERT INTO tickets_salida (id, estado) VALUES (17, 0);
INSERT INTO tickets_salida (id, estado) VALUES (18, 0);
INSERT INTO tickets_salida (id, estado) VALUES (19, 0);
INSERT INTO tickets_salida (id, estado) VALUES (20, 0);
INSERT INTO tickets_salida (id, estado) VALUES (21, 0);
INSERT INTO tickets_salida (id, estado) VALUES (22, 0);
INSERT INTO tickets_salida (id, estado) VALUES (23, 0);
INSERT INTO tickets_salida (id, estado) VALUES (24, 0);
INSERT INTO tickets_salida (id, estado) VALUES (25, 0);
INSERT INTO tickets_salida (id, estado) VALUES (26, 0);
INSERT INTO tickets_salida (id, estado) VALUES (27, 0);
INSERT INTO tickets_salida (id, estado) VALUES (28, 0);
INSERT INTO tickets_salida (id, estado) VALUES (29, 0);
INSERT INTO tickets_salida (id, estado) VALUES (30, 0);
INSERT INTO tickets_salida (id, estado) VALUES (31, 0);
INSERT INTO tickets_salida (id, estado) VALUES (32, 0);
INSERT INTO tickets_salida (id, estado) VALUES (33, 0);
INSERT INTO tickets_salida (id, estado) VALUES (34, 0);
INSERT INTO tickets_salida (id, estado) VALUES (35, 0);
INSERT INTO tickets_salida (id, estado) VALUES (36, 0);
INSERT INTO tickets_salida (id, estado) VALUES (37, 0);
INSERT INTO tickets_salida (id, estado) VALUES (38, 0);
INSERT INTO tickets_salida (id, estado) VALUES (39, 0);
INSERT INTO tickets_salida (id, estado) VALUES (40, 0);
INSERT INTO tickets_salida (id, estado) VALUES (41, 0);
INSERT INTO tickets_salida (id, estado) VALUES (42, 0);
INSERT INTO tickets_salida (id, estado) VALUES (43, 0);
INSERT INTO tickets_salida (id, estado) VALUES (44, 0);
INSERT INTO tickets_salida (id, estado) VALUES (45, 0);
INSERT INTO tickets_salida (id, estado) VALUES (46, 0);
INSERT INTO tickets_salida (id, estado) VALUES (47, 0);
INSERT INTO tickets_salida (id, estado) VALUES (48, 0);
INSERT INTO tickets_salida (id, estado) VALUES (49, 0);
INSERT INTO tickets_salida (id, estado) VALUES (50, 0);
INSERT INTO tickets_salida (id, estado) VALUES (51, 0);
INSERT INTO tickets_salida (id, estado) VALUES (52, 0);
INSERT INTO tickets_salida (id, estado) VALUES (53, 0);
INSERT INTO tickets_salida (id, estado) VALUES (54, 0);
INSERT INTO tickets_salida (id, estado) VALUES (55, 0);
INSERT INTO tickets_salida (id, estado) VALUES (56, 0);
INSERT INTO tickets_salida (id, estado) VALUES (57, 0);
INSERT INTO tickets_salida (id, estado) VALUES (58, 0);
INSERT INTO tickets_salida (id, estado) VALUES (59, 0);
INSERT INTO tickets_salida (id, estado) VALUES (60, 0);
INSERT INTO tickets_salida (id, estado) VALUES (61, 0);
INSERT INTO tickets_salida (id, estado) VALUES (62, 0);
INSERT INTO tickets_salida (id, estado) VALUES (63, 0);
INSERT INTO tickets_salida (id, estado) VALUES (64, 0);
INSERT INTO tickets_salida (id, estado) VALUES (65, 0);
INSERT INTO tickets_salida (id, estado) VALUES (66, 0);
INSERT INTO tickets_salida (id, estado) VALUES (67, 0);
INSERT INTO tickets_salida (id, estado) VALUES (68, 0);
INSERT INTO tickets_salida (id, estado) VALUES (69, 0);
INSERT INTO tickets_salida (id, estado) VALUES (70, 0);
INSERT INTO tickets_salida (id, estado) VALUES (71, 0);
INSERT INTO tickets_salida (id, estado) VALUES (72, 0);
INSERT INTO tickets_salida (id, estado) VALUES (73, 0);
INSERT INTO tickets_salida (id, estado) VALUES (74, 0);
INSERT INTO tickets_salida (id, estado) VALUES (75, 0);
INSERT INTO tickets_salida (id, estado) VALUES (76, 0);
INSERT INTO tickets_salida (id, estado) VALUES (77, 0);
INSERT INTO tickets_salida (id, estado) VALUES (78, 0);
INSERT INTO tickets_salida (id, estado) VALUES (79, 0);
INSERT INTO tickets_salida (id, estado) VALUES (80, 0);
INSERT INTO tickets_salida (id, estado) VALUES (81, 0);
INSERT INTO tickets_salida (id, estado) VALUES (82, 0);
INSERT INTO tickets_salida (id, estado) VALUES (83, 0);
INSERT INTO tickets_salida (id, estado) VALUES (84, 0);
INSERT INTO tickets_salida (id, estado) VALUES (85, 0);
INSERT INTO tickets_salida (id, estado) VALUES (86, 0);
INSERT INTO tickets_salida (id, estado) VALUES (87, 0);
INSERT INTO tickets_salida (id, estado) VALUES (88, 0);
INSERT INTO tickets_salida (id, estado) VALUES (89, 0);
INSERT INTO tickets_salida (id, estado) VALUES (90, 0);
INSERT INTO tickets_salida (id, estado) VALUES (91, 0);
INSERT INTO tickets_salida (id, estado) VALUES (92, 0);
INSERT INTO tickets_salida (id, estado) VALUES (93, 0);
INSERT INTO tickets_salida (id, estado) VALUES (94, 0);
INSERT INTO tickets_salida (id, estado) VALUES (95, 0);
INSERT INTO tickets_salida (id, estado) VALUES (96, 0);
INSERT INTO tickets_salida (id, estado) VALUES (97, 0);
INSERT INTO tickets_salida (id, estado) VALUES (98, 0);
INSERT INTO tickets_salida (id, estado) VALUES (99, 0);
INSERT INTO tickets_salida (id, estado) VALUES (100, 0);