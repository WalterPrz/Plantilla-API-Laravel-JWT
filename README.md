# API LARAVEL CON JWT

### Características

-   Autenticación con JWT.
-   Recuperación de contraseña con email, con la plantilla en blade;
-   CRUDs de todas las entidades.

---

### Requisitos

-   Base de datos postgresSQL
-   Php 8+
-   Composer
-   Docker

---

### Instalación

1. Clonar el proyecto:

```bash
  git clonegit@github.com:WalterPrz/Plantilla-API-Laravel-JWT.git
```

2. Movernos a la carpeta del proyecot y realizar:

```bash
  composer install
```

3. Crear el archivo .env

```bash
  copy .env.example .env
```

4. Crear el archivo .env

```bash
  copy .env.example .env
```

5. Generar la llave:

```bash
  php artisan key:generate
```

6. Crear una base de datos con cotejamiento: utf8_unicode_ci

7. Editar el archivo .env con las credenciales para la conexión a la base de datos.

| Nombre        | Descripción                          | Ejemplo       |
| :------------ | :----------------------------------- | :------------ |
| DB_CONNECTION | Tipo de base de datos                | `psql`        |
| DB_HOST       | Dirección o IP de la base de datos   | `127.0.0.1`   |
| DB_PORT       | Puerto de conexión de la base        | `3306`        |
| DB_DATABASE   | Nombre de la base de datos           | `api-laravel` |
| DB_USERNAME   | Usuario de la base de datos          | `root`        |
| DB_PASSWORD   | Clave de usuario de la base de datos | `admin`       |

8 Editar otras credenciales del .env
Para el enviar correo electrónico:

| Nombre          | Descripción                                      | Ejemplo                |
| :-------------- | :----------------------------------------------- | :--------------------- |
| MAIL_MAILER     | Tipo de protocolo                                | `smtp`                 |
| MAIL_HOST       | Url del host                                     | `smtp.gmail.com`       |
| MAIL_PORT       | Puerto de conexión al host                       | `456`                  |
| MAIL_USERNAME   | Email que enviará los correos                    | `laravel@gmail.com`    |
| MAIL_PASSWORD   | Contraseña del email para ser usado por terceros | ` abcd abcd abcd abcd` |
| MAIL_ENCRYPTION | Encryptación del email                           | `tls`                  |

9 Otras configuraciones de variable de entorno.

| Nombre                            | Descripción                                                                      | Ejemplo                       |
| :-------------------------------- | :------------------------------------------------------------------------------- | :---------------------------- |
| EMAIL_ADMIN                       | Email para el primer usuario admin                                               | `admin@example.com`           |
| PASSWORD_ADMIN                    | Contraseña para el primer usuario admin                                          | `admin`                       |
| FRONTEND_URL                      | Url del server de frontend                                                       | `http://localhost:3000`       |
| FRONTEND_VIEW_PATH_PASSWORD_RESET | Path del frontend donde estará la vista para recuperar crear la nueva contraseña | `/inicio/cambiar-contraseña/` |
| ALLOW_VALIDATION_BY_PERMISO       | Permitir o no la validación por permiso en cada endpoint                         | `true`                        |

Todas las demas variables de entorno dejarlas tal y como están.

10 Realizar la migracion

```bash
php artisan migrate
```

11 Realizar la migracion

```bash
php artisan migrate
```

12 Ingresar los seeders

```bash
php artisan db:seed
```
