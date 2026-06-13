# Huellitas - Parte 1

Juan Diego Sotomayor

## Descripción general

Huellitas es una aplicación web desarrollada con Laravel para apoyar la
gestión de adopciones de mascotas. Esta versión corresponde a la Parte 1 del
proyecto y establece la base relacional, administrativa y de seguridad que
necesita el flujo de adopción.

La aplicación todavía no representa la entrega final. El catálogo público y
el proceso completo de solicitudes, citas, cumplimientos y adopciones
corresponden a una segunda etapa.

## Objetivo de esta entrega

La Parte 1 tiene como objetivo proporcionar una base estable para que el
administrador pueda gestionar la información principal del sistema y para que
la Parte 2 pueda utilizar las migraciones, modelos, relaciones y datos
iniciales sin redefinir el modelo relacional.

## Alcance implementado

- Modelo relacional de las 12 tablas definidas para Huellitas.
- Migraciones con claves foráneas, restricciones y marcas de tiempo.
- Modelos Eloquent con relaciones, asignación masiva y conversiones de tipos.
- Seeders para roles, usuarios, tipos, razas, mascotas, requisitos e imágenes.
- Inicio y cierre de sesión.
- Registro público de usuarios con rol Adoptante.
- Registro de adoptantes desde el panel administrativo.
- Autorización del panel mediante los roles Administrador y Adoptante.
- Middleware para proteger las rutas administrativas.
- Dashboard con resumen de los registros principales.
- CRUD administrativo de tipos de mascotas.
- CRUD administrativo de razas.
- CRUD administrativo de mascotas.
- CRUD administrativo de requisitos de adopción.
- Asociación simple de imágenes ubicadas en `public/img/mascotas/`.
- Interfaz administrativa con Blade y Bootstrap 5.

## Funcionalidades principales

### Seguridad y roles

Los administradores y adoptantes se almacenan en la tabla `users` y se
diferencian mediante la tabla `roles`. No existe una tabla separada para
administradores. Las contraseñas se almacenan mediante el sistema de hash de
Laravel.

Las rutas del panel usan los middleware `auth` y `admin`. Un adoptante
autenticado no puede acceder a los módulos administrativos.

### Gestión administrativa

El panel permite consultar y mantener tipos de mascotas, razas, mascotas y
requisitos. Los tipos y las razas que ya están relacionados con mascotas no se
pueden eliminar.

En el formulario de mascotas primero se selecciona el tipo y después una raza.
El selector de raza incluye búsqueda instantánea y solo muestra las opciones
del tipo elegido.

### Requisitos de adopción

Los requisitos pueden registrarse para un tipo concreto o generarse para todos
los tipos existentes. En ambos casos se conserva el campo obligatorio
`tipo_mascota_id`. Cuando el requisito es general, el administrador puede
indicar para cuáles tipos será obligatorio.

### Imágenes de mascotas

La gestión de imágenes es intencionalmente sencilla. Los archivos se colocan
en `public/img/mascotas/` y el panel registra su ruta relativa, además de
permitir indicar una imagen principal.

## Funcionalidades pendientes

La Parte 2 debe completar:

- Layout y navegación pública.
- Catálogo público de mascotas.
- Formulario y seguimiento de solicitudes de adopción.
- Revisión y respuesta de solicitudes.
- Programación y gestión de citas.
- Registro de cumplimiento de requisitos.
- Registro final de adopciones.

Estas funcionalidades no se presentan como implementadas en esta entrega.

## Tecnologías utilizadas

| Tecnología | Uso en el proyecto |
|---|---|
| PHP 8.3 o superior | Lenguaje principal |
| Laravel 13 | Framework de la aplicación |
| MariaDB/MySQL | Persistencia de datos |
| Eloquent ORM | Modelos y relaciones |
| Blade | Plantillas del servidor |
| Bootstrap 5 | Interfaz y componentes visuales |
| JavaScript | Interacciones básicas de formularios |
| Vite y Node.js | Gestión y compilación de recursos |
| PHPUnit | Pruebas automatizadas |
| Git | Control de versiones y trabajo por ramas |

## Estructura principal del proyecto

| Ruta | Contenido |
|---|---|
| `app/Models/` | Modelos y relaciones Eloquent |
| `app/Http/Controllers/Admin/` | Controladores del panel administrativo |
| `app/Http/Controllers/Auth/` | Inicio de sesión, registro y cierre de sesión |
| `app/Http/Middleware/` | Middleware de autorización administrativa |
| `app/Http/Requests/` | Validaciones de formularios |
| `database/migrations/` | Definición de tablas y claves foráneas |
| `database/seeders/` | Datos iniciales para desarrollo |
| `resources/views/admin/` | Vistas de dashboard y CRUD administrativos |
| `resources/views/auth/` | Formularios de autenticación y registro |
| `resources/views/layouts/` | Layouts compartidos |
| `public/css/huellitas.css` | Estilos base de la aplicación |
| `public/img/mascotas/` | Imágenes simples de mascotas |
| `routes/web.php` | Rutas públicas, de autenticación y administrativas |
| `tests/Feature/` | Pruebas de base de datos, seguridad y CRUD |

## Requisitos previos

- PHP 8.3 o superior con la extensión `pdo_mysql`.
- Composer.
- MariaDB o MySQL.
- Node.js y npm.
- Git.

Para comprobar la extensión de base de datos:

```bash
php -m | grep -i pdo_mysql
```

## Configuración de la base de datos

El nombre utilizado por defecto es `huellitas_db`. Puede crearse desde el
cliente de MariaDB/MySQL:

```sql
CREATE DATABASE huellitas_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

El archivo `.env.example` está preparado para MariaDB y no contiene
contraseñas. Después de crear `.env`, configura las credenciales locales:

```dotenv
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=huellitas_db
DB_USERNAME=root
DB_PASSWORD=
```

Si se utiliza MySQL, `DB_CONNECTION` puede cambiarse a `mysql`.

## Instalación y configuración

```bash
git clone https://github.com/jdsotomayorjimenez/proyecto_ldp.git
cd proyecto_ldp
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan config:clear
```

El archivo `.env` contiene configuración local y está excluido del repositorio.

## Migraciones y datos iniciales

Para construir la base y cargar los datos de desarrollo:

```bash
php artisan migrate:fresh --seed
```

Este comando elimina las tablas existentes de la base configurada antes de
recrearlas. Debe ejecutarse únicamente sobre una base local de desarrollo.

Los tipos iniciales son Perro, Gato, Conejo y Ave. Se pueden crear tipos
concretos adicionales desde el panel; no se utiliza una categoría genérica
`Otro`.

## Credenciales de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@huellitas.com` | `password` |
| Adoptante | `adoptante@huellitas.com` | `password` |

Estas credenciales se generan mediante seeders y son exclusivamente para
desarrollo.

## Ejecución del proyecto

Inicia el servidor de Laravel:

```bash
php artisan serve
```

La aplicación estará disponible normalmente en
`http://127.0.0.1:8000`.

Los layouts actuales cargan Bootstrap mediante CDN. Para trabajar con los
recursos administrados por Vite también puede ejecutarse:

```bash
npm run dev
```

Rutas principales:

| Ruta | Función |
|---|---|
| `/` | Página de inicio |
| `/login` | Inicio de sesión |
| `/registro` | Registro público de adoptantes |
| `/admin/dashboard` | Dashboard administrativo |
| `/admin/tipos-mascotas` | Gestión de tipos |
| `/admin/razas` | Gestión de razas |
| `/admin/mascotas` | Gestión de mascotas e imágenes |
| `/admin/requisitos` | Gestión de requisitos |
| `/admin/adoptantes/crear` | Registro administrativo de adoptantes |

## Verificación

Comandos útiles para revisar la instalación:

```bash
php artisan about
php artisan route:list
php artisan migrate:status
npm run build
```

Las pruebas utilizan `RefreshDatabase`, por lo que deben ejecutarse sobre una
base temporal y no sobre `huellitas_db`:

```bash
mariadb -u root -p -e \
  "CREATE DATABASE huellitas_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

DB_CONNECTION=mariadb DB_DATABASE=huellitas_test php artisan test

mariadb -u root -p -e "DROP DATABASE huellitas_test;"
```

Las pruebas automatizadas cubren el contrato de la base de datos, la
autenticación, la autorización administrativa y operaciones principales de
los CRUD.

## Estado actual

La Parte 1 está implementada como primera entrega funcional. Proporciona la
base administrativa y relacional necesaria para que la rama encargada de la
Parte 2 continúe con la experiencia pública y el flujo completo de adopción.

La distribución acordada es:

- `branch-jd`: Parte 1, base de datos, modelos, seguridad y administración.
- `branch-kg`: Parte 2, vista pública y flujo completo de adopción.

Las migraciones, modelos y seeders compartidos deben modificarse de forma
coordinada para evitar incompatibilidades entre ambas partes.

## Nota final

Este README documenta el estado de la primera entrega funcional de Huellitas.
No describe como terminadas las funciones reservadas para la Parte 2.

Juan Diego Sotomayor
