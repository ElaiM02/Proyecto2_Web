CREATE TABLE rol (
    id_rol        INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(50) NOT NULL UNIQUE  -- 'SUPERADMIN', 'OPERADOR', 'USUARIO'
);

CREATE TABLE usuario (
    id_usuario    INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,         -- generado con password_hash()
    id_rol        INT NOT NULL,
    activo        TINYINT(1) NOT NULL DEFAULT 1, -- desactivar en lugar de borrar
    creado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NULL,

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

CREATE TABLE ticket_tipo (
    id_tipo_ticket INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(50) NOT NULL UNIQUE  -- 'PETICION', 'INCIDENTE'
);

CREATE TABLE ticket_estado (
    id_estado_ticket INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(50) NOT NULL unique,  -- ej: 'NO_ASIGNADO','ASIGNADO',...
    descripcion      VARCHAR(255) NULL
);

CREATE TABLE ticket (
    id_ticket        INT AUTO_INCREMENT PRIMARY KEY,
    titulo           VARCHAR(200) NOT NULL,
    descripcion_inicial TEXT NOT NULL,           -- primer mensaje/entrada descriptiva
    id_tipo_ticket   INT NOT NULL,
    id_estado_ticket INT NOT NULL,               -- estado actual
    id_usuario_creador INT NOT NULL,             -- quien creó el ticket (rol: Usuario)
    id_operador_asignado INT NULL,               -- operador actual, si existe
    creado_en        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en   DATETIME NULL,

    CONSTRAINT fk_ticket_tipo
        FOREIGN KEY (id_tipo_ticket) REFERENCES ticket_tipo(id_tipo_ticket),

    CONSTRAINT fk_ticket_estado
        FOREIGN KEY (id_estado_ticket) REFERENCES ticket_estado(id_estado_ticket),

    CONSTRAINT fk_ticket_creador
        FOREIGN KEY (id_usuario_creador) REFERENCES usuario(id_usuario),

    CONSTRAINT fk_ticket_operador_asignado
        FOREIGN KEY (id_operador_asignado) REFERENCES usuario(id_usuario)
);

CREATE TABLE ticket_entrada (
    id_entrada      INT AUTO_INCREMENT PRIMARY KEY,
    id_ticket       INT NOT NULL,
    id_autor        INT NOT NULL,         -- puede ser Usuario o Operador
    texto           TEXT NOT NULL,        -- comentario / descripción
    id_estado_anterior INT NULL,          -- estado antes del cambio (si aplica)
    id_estado_nuevo    INT NULL,          -- estado después del cambio (si aplica)
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_entrada_ticket
        FOREIGN KEY (id_ticket) REFERENCES ticket(id_ticket),

    CONSTRAINT fk_entrada_autor
        FOREIGN KEY (id_autor) REFERENCES usuario(id_usuario),

    CONSTRAINT fk_entrada_estado_anterior
        FOREIGN KEY (id_estado_anterior) REFERENCES ticket_estado(id_estado_ticket),

    CONSTRAINT fk_entrada_estado_nuevo
        FOREIGN KEY (id_estado_nuevo) REFERENCES ticket_estado(id_estado_ticket)
);

CREATE TABLE categoria (
    id_categoria   INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL UNIQUE,
    descripcion    VARCHAR(255) NULL
);

CREATE TABLE prioridad (
    id_prioridad   INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(50) NOT NULL UNIQUE,  -- 'BAJA','MEDIA','ALTA','CRITICA'
    nivel          TINYINT NOT NULL              -- 1,2,3,4
);




-- Roles
INSERT INTO rol (nombre) VALUES ('SUPERADMIN'), ('OPERADOR'), ('USUARIO');

-- Usuarios de ejemplo (contraseña para todos: "123456")
INSERT INTO usuario (nombre_completo, username, password_hash, id_rol) VALUES
('Ana Gómez',           'ana.gomez',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3), -- USUARIO
('Carlos Pérez',        'carlos.perez',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3), -- USUARIO
('Luis Ramírez',        'luis.ramirez',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2), -- OPERADOR
('María Fernández',     'maria.fernandez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2), -- OPERADOR
('Admin Sistema',       'admin',           '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1); -- SUPERADMIN

-- Tipos de ticket
INSERT INTO ticket_tipo (nombre) VALUES ('PETICION'), ('INCIDENTE');

-- Estados de ticket
INSERT INTO ticket_estado (id_estado_ticket, nombre, descripcion) VALUES
(1, 'NO_ASIGNADO',        'Ticket recién creado, aún sin operador'),
(2, 'ASIGNADO',           'Operador ya tomó el ticket'),
(3, 'EN_PROCESO',         'Operador trabajando en la solución'),
(4, 'EN_ESPERA',          'Esperando respuesta de tercero o usuario'),
(5, 'SOLUCIONADO',        'El operador marcó como resuelto'),
(6, 'CERRADO',            'El usuario aceptó la solución');

INSERT INTO ticket (titulo, descripcion_inicial, id_tipo_ticket, id_estado_ticket, id_usuario_creador, id_operador_asignado) VALUES
('No me llega el correo corporativo', 'Desde ayer no recibo correos en Outlook. Ya reinicié la PC varias veces.', 2, 3, 1, 3),
('Solicitud de acceso a carpeta compartida', 'Necesito acceso de lectura/escritura a \\servidor\Proyectos\2025', 1, 2, 2, 4),
('Impresora del piso 3 no responde', 'La impresora HP marca "Error 79" y no imprime nada.', 2, 1, 1, NULL),
('Instalación de Adobe Acrobat Pro', 'Por favor instalar Adobe Acrobat en mi equipo para firmar documentos.', 1, 5, 2, 3),
('Pantalla azul en equipo de contabilidad', 'El equipo de Sandra se reinicia cada 20 minutos con pantalla azul. ¡Urgente!', 2, 4, 1, 4);


ALTER TABLE ticket 
ADD COLUMN imagen VARCHAR(255) NULL AFTER descripcion_inicial;