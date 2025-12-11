# HelpDesk

**2° Proyecto – 25% – ISW-613 Programación Web I**  
**Universidad Tecnológica Nacional (UTN)**  
**Profesor:** Misael Matamoros Soto <mmatamoros@utn.ac.cr>  

**Fecha de entrega:** 11/12/2025  

---

## Objetivo
Aplicar los conocimientos adquiridos en programación del lado del servidor con PHP, manejo de sesiones, validación de formularios, persistencia de datos, arquitectura MVC básica y buenas prácticas de desarrollo web, mediante la creación de una aplicación web dinámica para la gestión de solicitudes internas de soporte técnico.

---

## Descripción del Proyecto
La Dirección de Tecnologías de Información de la empresa «CSGO» requiere un sistema interno para la gestión de solicitudes de soporte técnico. El sistema permitirá a los usuarios internos reportar incidentes y hacer peticiones, mientras que los operadores del departamento puedan gestionarlas.

El sistema es **monolítico** (interfaz de usuario, lógica de negocio y acceso a datos integrados en una sola unidad), desarrollado en **PHP puro sin frameworks**, siguiendo un patrón **MVC propio**.

---

## Características Implementadas
- Autenticación segura con **sesiones PHP** y roles: `SUPERADMIN`, `OPERADOR`, `USUARIO`.
- CRUD completo de usuarios (**solo accesible por SUPERADMIN**).
- Gestión de tickets con flujo de estados profesional:
  - No Asignado (inicial)  
  - Asignado (operador se autoasigna)  
  - En Proceso  
  - En Espera de Terceros  
  - Solucionado  
  - Cerrado (usuario acepta la solución)
- **Aceptar/Denegar solución:** Cuando el operador marca "Solucionado", el usuario puede:
  - Aceptar → cierra el ticket  
  - Denegar → reabre en "Asignado" con comentario obligatorio
- Subida de imágenes adjuntas al crear ticket (validación de tamaño y tipo)
- Historial inmutable de cambios (registrado en `ticket_entrada`)
- Filtros y búsquedas avanzadas en listado de tickets
- Interfaz **responsive** con Bootstrap 5
- Código limpio, comentado y buenas prácticas (validaciones, protección XSS, transacciones BD)

---

## Restricciones Técnicas Cumplidas
- Base de datos: **DBeaver/MySQL**
- Stack tecnológico: **HTML, CSS, JavaScript + PHP**
- **No frameworks ni micro-frameworks** (Laravel, Symfony, CodeIgniter, etc.)
- Licenciamiento: todas las tecnologías utilizadas son **código abierto**
- Arquitectura: **Aplicación monolítica** (todos los componentes integrados en una sola unidad)

---

## Tecnologías Utilizadas
- PHP 8+
- MySQL/MariaDB
- Bootstrap 5
- HTML5, CSS3, JavaScript vanilla
- Patrón MVC propio
- PDO para acceso a base de datos
- Sesiones PHP nativas

---

## Estructura del Proyecto

```bash
helpdesk/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Core/
│   └── Views/
├── public/
│   ├── uploads/
│   ├── css/
│   └── index.php
├── database/S
└── README.md
```

## Integrantes del Equipo
- Edgar Eliam Araya Alvarado
- Jose Pablo Chaves 