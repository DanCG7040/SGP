USE sgp_pacientes;

INSERT INTO departamentos (nombre) VALUES
('Cundinamarca'),
('Antioquia'),
('Valle del Cauca'),
('Atlántico'),
('Santander'),
('Tolima');

INSERT INTO municipios (departamento_id, nombre) VALUES
(1, 'Bogotá'),
(1, 'Chía'),
(2, 'Medellín'),
(2, 'Bello'),
(3, 'Cali'),
(3, 'Palmira'),
(4, 'Barranquilla'),
(4, 'Soledad'),
(5, 'Bucaramanga'),
(5, 'Floridablanca'),
(6, 'Ibague');

INSERT INTO tipos_documento (nombre) VALUES
('Cédula de Ciudadanía'),
('Tarjeta de Identidad');

INSERT INTO genero (nombre) VALUES
('Masculino'),
('Femenino'),
('Otro');

INSERT INTO users (username, email, password) VALUES
('admin', 'admin@sgp.com', '$2y$12$jOf82y6nLOY6qukofduyseKFoMvGBu8/6wanaZKO.4t9P5/9Olh7a');

INSERT INTO paciente (
    tipo_documento_id, 
    numero_documento, 
    nombre1, 
    nombre2, 
    apellido1, 
    apellido2, 
    genero_id, 
    departamento_id, 
    municipio_id, 
    correo
) VALUES
(1, '1000123456', 'Juan', 'Carlos', 'Pérez', 'García', 1, 1, 1, 'juan.perez@email.com', NULL),
(1, '2000234567', 'María', 'Alejandra', 'González', 'López', 2, 2, 3, 'maria.gonzalez@email.com', NULL),
(1, '3000345678', 'Carlos', 'Andrés', 'Rodríguez', 'Martínez', 1, 3, 5, 'carlos.rodriguez@email.com', NULL),
(1, '4000456789', 'Ana', 'Sofía', 'Hernández', 'Sánchez', 2, 4, 7, 'ana.hernandez@email.com', NULL),
(1, '5000567890', 'Luis', 'Fernando', 'Torres', 'Ramírez', 1, 5, 9, 'luis.torres@email.com', NULL);
(1, '1234567890', 'María', 'Isabel', 'González', 'Pérez', 2, 1, 1, 'maria.gonzalez@email.com', NULL),
(1, '2345678901', 'Carlos', 'Alberto', 'Rodríguez', 'Martínez', 1, 1, 2, 'carlos.rodriguez@email.com', NULL),
(2, '7612345663', 'Ana', 'Sofía', 'López', 'García', 2, 2, 3, 'ana.lopez@email.com', NULL),
(1, '3456789012', 'Juan', 'Pablo', 'Sánchez', 'Fernández', 1, 2, 4, 'juan.sanchez@email.com', NULL),
(2, 'CD23456778', 'Laura', 'Valentina', 'Torres', 'Ramírez', 2, 3, 5, 'laura.torres@email.com', NULL),
(1, '4567890123', 'Diego', 'Alejandro', 'Morales', 'Vargas', 1, 3, 6, 'diego.morales@email.com', NULL),
(1, '5678901234', 'Sandra', 'Milena', 'Castro', 'Jiménez', 2, 4, 7, 'sandra.castro@email.com', NULL),
(2, '3454567278', 'Andrés', 'Felipe', 'Ruiz', 'Moreno', 1, 4, 8, 'andres.ruiz@email.com', NULL),
(6, '6789012345', 'Paola', 'Andrea', 'Herrera', 'Ospina', 2, 5, 9, 'paola.herrera@email.com', NULL),
(2, '6745678912', 'Ricardo', 'José', 'Mendoza', 'Arias', 1, 5, 10, 'ricardo.mendoza@email.com', NULL);
