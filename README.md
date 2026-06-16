# Huellitas — Sistema de Gestión de Adopciones de Mascotas

Plataforma integral desarrollada en Laravel para la gestión del proceso de adopción de mascotas, desde la administración del catálogo hasta la formalización del acta de adopción.

---

## Descripción

**Huellitas** es una aplicación web enfocada en la automatización y formalización del flujo de adopción. El sistema permite la consulta de un catálogo de registros, procesamiento de solicitudes y seguimiento de estados, proporcionando a los administradores herramientas para la gestión de entidades, evaluación de candidatos, programación de citas y verificación del cumplimiento de requisitos legales y de bienestar animal.

| Parte | Responsable | Descripción general |
| --- | --- | --- |
| Parte 1 — Arquitectura de Datos y Backend | Juan Diego Sotomayor | Diseño y construcción de la base de datos relacional, modelos Eloquent y poblado inicial de datos (seeders). |
| Parte 2 — Interfaz y Lógica de Negocio | Karel González | Desarrollo de la interfaz de usuario, controladores, validaciones y lógica de transición de estados. |

---

## Arquitectura General

El sistema implementa el patrón de arquitectura Modelo-Vista-Controlador (MVC) utilizando el framework Laravel, con Eloquent ORM para la capa de persistencia de datos y MariaDB como motor de base de datos relacional.

```text
[Cliente]
   ↓
[Capa de Presentación (Blade / Bootstrap 5)]
   ↓
[Enrutador / Controladores]
   ↓
[Capa de Persistencia (Eloquent ORM) / MariaDB]
```

### Distribución de Componentes

```text
[Juan Diego Sotomayor]
└── Parte 1: Base de Datos y Estructura
    ├── Diseño del Modelo Relacional (12 tablas)
    ├── Implementación de Migraciones y Restricciones
    └── Definición de Modelos y Relaciones Eloquent

[Karel González]
└── Parte 2: Lógica y Frontend
    ├── Desarrollo de Vistas y Estilos
    ├── Lógica de Controladores y Flujo de Estados
    └── Implementación de Validaciones y Seguridad
```

---

## Flujo del Sistema

```text
[Selección de mascota en catálogo]
   ↓
[Creación de Solicitud de Adopción]
   ↓
[Evaluación Administrativa (Aprobación/Rechazo)]
   ↓
[Generación de Cita y Asignación de Requisitos]
   ↓
[Verificación de Cumplimiento de Requisitos]
   ↓
[Registro de Transacción Final (Adopción)]
```

---

## Stack Tecnológico

| Componente | Tecnología |
| --- | --- |
| Lógica de Servidor | PHP 8.3 / Laravel 13 |
| Motor de Base de Datos | MariaDB |
| Capa de Presentación | Blade / Bootstrap 5 / CSS3 |
| Pruebas Unitarias | PHPUnit |
| Empaquetador de Módulos | Vite / Node.js |

---

## Estructura del Repositorio

```text
proyecto-huellitas/
├── app/
│   ├── Http/Controllers/      # Controladores y lógica de negocio
│   ├── Models/                # Modelos ORM
│   └── Middleware/            # Filtros de interceptación
├── database/
│   ├── migrations/            # Esquemas de base de datos
│   └── seeders/               # Scripts de inserción de datos
├── resources/
│   └── views/                 # Plantillas de renderizado
├── public/
│   ├── css/                   # Hojas de estilo compiladas
│   └── img/mascotas/          # Almacenamiento de imágenes
└── README.md
```

---

## Parte 1 — Arquitectura de Datos y Backend

Responsable: **Juan Diego Sotomayor**

### Descripción

Construcción del modelo relacional normalizado para soportar la integridad transaccional del flujo de adopción.

### Responsabilidades principales

* Diseño de esquema relacional de 12 tablas interconectadas.
* Programación de migraciones con restricciones de integridad referencial estricta.
* Configuración de modelos Eloquent (`belongsTo`, `hasMany`, `hasOne`) con lógica de negocio integrada.
* Desarrollo de seeders para la automatización del estado inicial del catálogo y usuarios.

---

## Parte 2 — Interfaz y Lógica de Negocio

Responsable: **Karel González**

### Descripción

Implementación de la interfaz de usuario, control de acceso y codificación de las reglas de negocio del sistema.

### Responsabilidades principales

* Desarrollo de las interfaces responsivas para cliente y panel de administración.
* Implementación de autenticación y control de acceso basado en roles (RBAC).
* Programación de la máquina de estados para el ciclo de vida de las solicitudes (Pendiente, Aprobada, Rechazada, Adoptada).
* Generación de la lógica para el seguimiento de requisitos y cierre de adopciones.

---

## Integración de Componentes

La capa de presentación (Parte 2) se comunica con la capa de datos (Parte 1) a través de las abstracciones proporcionadas por Eloquent ORM.

| Proceso | Flujo de Integración |
| --- | --- |
| Suministro | Parte 1 provee la estructura relacional y los datos iniciales. |
| Consumo | Parte 2 instancia objetos y colecciones a partir de los modelos. |
| Mutación | Parte 2 aplica las reglas de negocio y modifica el estado de los objetos. |
| Persistencia | Parte 1 asegura la integridad referencial al confirmar las transacciones. |

---

## Decisiones de Ingeniería de Software

* **Framework Laravel**: Seleccionado por la eficiencia en la gestión del ORM y los mecanismos de seguridad integrados (protección CSRF, inyección SQL, XSS).
* **Control de Acceso**: Consolidación de entidades de usuario en una única tabla con diferenciación mediante el atributo `role_id` para simplificar la autenticación.
* **Integridad de Datos**: Aplicación de restricciones de borrado (`onDelete('restrict')`) en tablas de catálogo (tipos, razas) para prevenir inconsistencias en registros históricos.
* **Arquitectura de Trabajo**: Separación estricta de las capas de persistencia y presentación para habilitar el desarrollo concurrente y modular.

---

## Instalación y Ejecución

```bash
# Clonación del repositorio
git clone https://github.com/jdsotomayorjimenez/proyecto_ldp.git
cd proyecto_ldp

# Instalación de dependencias
composer install
npm install

# Configuración de entorno
cp .env.example .env
php artisan key:generate

# Migración de esquemas e inicialización de datos
php artisan migrate:fresh --seed

# Ejecución del servidor de desarrollo
php artisan serve
```

---

## Credenciales de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@huellitas.com` | `password` |
| Adoptante | `adoptante@huellitas.com` | `password` |

---

## Nota de Autoría

Este proyecto representa el esfuerzo conjunto para modernizar la gestión de adopciones, dividiendo el trabajo en una base de datos sólida y una lógica de negocio interactiva.

**Juan Diego Sotomayor** | **Karel González**
