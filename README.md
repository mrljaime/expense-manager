# Symfony local environment
En este proyecto se encuentra un ambiente local para desarrollar proyectos con Symfony

---
# Pa' empezar
## Requerimientos
<div class="table">

| Requerimiento                                             | Descripción                                                                                                                                                                                                                      |
|-----------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Docker](https://www.docker.com/products/docker-desktop/) | La idea es que puedas ocupar este ambiente en cualquier computadora. Es suficiente con tener Docker para trabajar.                                                                                                               |
| [Compose](https://docs.docker.com/compose/)               | Docker se ocupa para poder correr los contenedores, pero compose se utiliza para aplicar un comando para arrancar varios contenedores al mismo tiempo y que éstos estén conectados de manera sencilla a través de configuración. |

</div>

## ¿Qué contiene este proyecto?
La idea de clonar este proyecto es que puedas ejecutar `docker compose up` y que se arranquen un par de contenedores:
* Uno de PHP con FPM que nos ayudará a correr todos los comandos de PHP y Symfony
* Uno de MariaDB que se usará para guardar datitos en nuestro proyecto
* Uno de Nginx que expone por HTTP nuestro Syfmony sin tener que correr el comando `php -S`  


## Para configurar antes de arrancar
Es necesario que le eches una ojito al archivo `.env.compose` donde se utilizan algunas variables de ambiente para
determinar valores generales de ambiente.  

Aquí te pongo una tablita con la variables de ambiente más importantes y para qué sirven:
<div class="table-3">

| Variable                 | Descripción                                                                                                                                                                                                                                                                                                 | Default                   |
|--------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------|
| `DATABASE_ROOT_PASSWORD` | Se utiliza para configurar la contraseña del usuario `root` de MariaDB                                                                                                                                                                                                                                      | `lotus_password_root_123` |
| `DATABASE_NAME`          | Se utiliza para establecer el nombre de la base de datos al arrancar el contenedor de MariaDB<br/><br/>Se usa también para establecer la conexión de Symfony con la base de datos                                                                                                                           | `lotus`                   |
| `DATABASE_USERNAME`      | Se utiliza para colocar un nombre de usuario alternativo al usuario `root` que se creará junto con su contraseña donde el contenedor de MariaDB automáticamente le asigna los permisos `GRANT ALL` a la base de datos. <br/><br/>Se usa también para configurar la conexión de Symfony con la base de datos | `lotus`                   |
| `DATABASE_PASSWORD`      | La contraseña con la que se autenticará el nombre de usuario de la línea anterior<br/><br/>Se usa también para establecer la conexión de Symfony con la base de datos                                                                                                                                       | `lotus_password_123`      |
| `SYMFONY_VERSION`        | Para definir qué versión de Symfony quieres que instale el proyecto si no hay una versión instalada                                                                                                                                                                                                         | `6.4`                     |
| `UNIQUE_INSTALLATION`    | El nombre del archivo para determinar que es una instalación fresca o que no es necesario instalar symfony de cero. Si existe, ignorará la instalación de Symfony al arrancar los contenedores                                                                                                              | `composer.json`           |
</div>

# Viene lo bueno

> TBD

