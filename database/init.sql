-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tienda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS categorias;

-- Tabla categorías
CREATE TABLE categorias (
                            id VARCHAR(36) PRIMARY KEY,
                            nombre VARCHAR(255) NOT NULL UNIQUE,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                            is_deleted BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos categorías
INSERT INTO categorias (id, nombre) VALUES
                                        ('d69cf3db-b77d-4181-b3cd-5ca8107fb6a9', 'DEPORTES'),
                                        ('6dbcbf5e-8e1c-47cc-8578-7b0a33ebc154', 'COMIDA'),
                                        ('a3d3c7e2-1f5b-4d8a-9c2e-8f7a6b5c4d3e', 'BEBIDA'),
                                        ('b4e4d8f3-2g6c-5e9b-0d3f-9g8b7c6d5e4f', 'COMPLEMENTOS'),
                                        ('c5f5e9g4-3h7d-6f0c-1e4g-0h9c8d7e6f5g', 'OTROS');

-- Tabla productos
CREATE TABLE productos (
                           id BIGINT AUTO_INCREMENT PRIMARY KEY,
                           uuid VARCHAR(36) NOT NULL UNIQUE,
                           marca VARCHAR(255),
                           modelo VARCHAR(255),
                           descripcion VARCHAR(255),
                           precio DECIMAL(10, 2) DEFAULT 0.0,
                           stock INT DEFAULT 0,
                           imagen TEXT DEFAULT 'https://via.placeholder.com/150',
                           categoria_id VARCHAR(36),
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           is_deleted BOOLEAN DEFAULT FALSE,
                           FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos productos
INSERT INTO productos (uuid, marca, modelo, descripcion, precio, stock, categoria_id) VALUES
                                                                                          ('19135792-b778-441f-8767-0f1b3596a6d9', 'Nike', 'Air Max', 'Zapatillas deportivas', 89.99, 15, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
                                                                                          ('662ed342-de99-45c6-8468-41750a46cf51', 'Adidas', 'Ultraboost', 'Zapatillas running', 129.99, 25, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
                                                                                          ('b79182ad-91c3-46e8-9024-e1b84b08db41', 'Puma', 'Suede Classic', 'Zapatillas casual', 64.99, 10, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
                                                                                          ('4fa72b3f-dca2-4fd8-b81e-98a48a7e9c23', 'Reebok', 'Classic Leather', 'Zapatillas retro', 75.99, 8, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9'),
                                                                                          ('1e2584d8-db52-45da-b210-6d89f3e05ee0', 'New Balance', '574', 'Zapatillas urbanas', 84.99, 30, 'd69cf3db-b77d-4181-b3cd-5ca8107fb6a9');

-- Tabla usuarios
CREATE TABLE usuarios (
                          id BIGINT AUTO_INCREMENT PRIMARY KEY,
                          username VARCHAR(255) NOT NULL UNIQUE,
                          password VARCHAR(255) NOT NULL,
                          nombre VARCHAR(255) NOT NULL,
                          apellidos VARCHAR(255) NOT NULL,
                          email VARCHAR(255) NOT NULL UNIQUE,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
                          is_deleted BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos usuarios (contraseñas: admin=Admin1, user=User1234, test=test1234)
INSERT INTO usuarios (username, password, nombre, apellidos, email) VALUES
                                                                        ('admin', '$2a$10$vPaqZvZkz6jhb7U7k/V/v.5vprfNdOnh4sxi/qpPRkYTzPmFlI9p2', 'Admin', 'Administrador', 'admin@admin.com'),
                                                                        ('user', '$2a$12$RUq2ScW1Kiizu5K4gKoK4OTz80.DWaruhdyfi2lZCB.KeuXTBh0S.', 'User', 'Usuario Normal', 'user@user.com'),
                                                                        ('test', '$2a$10$Pd1yyq2NowcsDf4Cpf/ZXObYFkcycswqHAqBndE1wWJvYwRxlb.Pu', 'Test', 'Test Usuario', 'test@test.com');

-- Tabla user_roles
CREATE TABLE user_roles (
                            user_id BIGINT NOT NULL,
                            roles VARCHAR(255),
                            FOREIGN KEY (user_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos user_roles
INSERT INTO user_roles (user_id, roles) VALUES
                                            (1, 'USER'),
                                            (1, 'ADMIN'),
                                            (2, 'USER'),
                                            (3, 'USER');