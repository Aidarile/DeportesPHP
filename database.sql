-- 5a. Crear el modelo y la conexion a la BBDD --

CREATE DATABASE deportes_db;

USE deportes_db;

CREATE TABLE socio (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(128) NOT NULL,
    telefono VARCHAR(9),
    edad INT NOT NULL DEFAULT 0,
    penalizado BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id)
);

CREATE TABLE pista (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(128) NOT NULL,
    tipo VARCHAR(6) NOT NULL,
    max_jugadores INT NOT NULL DEFAULT 2,
    disponible BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id)
);

CREATE TABLE reserva (
    id INT NOT NULL AUTO_INCREMENT,
    socio INT NOT NULL,
    pista INT NOT NULL,
    fecha VARCHAR(8) NOT NULL,
    hora INT NOT NULL DEFAULT 0,
    iluminar BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (id)
);