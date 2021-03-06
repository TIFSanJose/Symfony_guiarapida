### Porblemas surgidos y soluciones encontradas 
___

##### > Conexion a Servidor Postgres
_Descripcion **Problema**:_ En paso 7.6 plantea la conexion desde el servidor web (php) al servidor bd (postgres), usando el comando symfony
    
    php.Cliente ----> pg.ServerDB
    <pre>
        symfony run psql 
    </pre>

no me funciono

_Descripcion **Solucion**:_
1. Se instalo el paquete psql, que es el cliente de pg, en servidor web (php). 
2. Luego la conexion se realizo usando los comandos para conexion remota de postgres mas symfony.
    <pre>
        symfony run psql -h ipHost -p puerto -d dbName -U userName

        symfony run psql -h 172.28.0.2 -p 5432 -U root -d symfony
    </pre>
##### < Conexion a Servidor Postgres

##### > Migracion de BD
_Descripcion **Problema**:_ Se trata de generar la clase de migracion a la DB de las entidades creadas, pero surge un error 

<pre> symfony console make:migration </pre>

<pre>
    OutPut:

    bash-5.1# symfony console make:migration
    
    [2021-04-21T13:53:10.103287+00:00] console.CRITICAL: Error thrown while running command "make:migration". Message: "An exception occurred in driver: SQLSTATE[HY000] [2002] No such file or directory" {"exception":"[object] (Doctrine\\DBAL\\Exception\\ConnectionException(code: 0): An exception occurred in driver: SQLSTATE[HY000] [2002] No such file or directory at /var/www/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/AbstractMySQLDriver.php:112)\n[previous exception] [object] (Doctrine\\DBAL\\Driver\\PDO\\Exception(code: 2002): SQLSTATE[HY000] [2002] No such file or directory at /var/www/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDO/Exception.php:18)\n[previous exception] [object] (PDOException(code: 2002): SQLSTATE[HY000] [2002] No such file or directory at /var/www/vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOConnection.php:39)","command":"make:migration","message":"An exception occurred in driver: SQLSTATE[HY000] [2002] No such file or directory"} []
    [2021-04-21T13:53:10.104410+00:00] console.DEBUG: Command "make:migration" exited with code "1" {"command":"make:migration","code":1} []

    In AbstractMySQLDriver.php line 112:
                                                                    
    An exception occurred in driver: SQLSTATE[HY000] [2002] No such   
    file or directory                                                 
                                                                    

    In Exception.php line 18:
                                                    
    SQLSTATE[HY000] [2002] No such file or directory  
                                                    

    In PDOConnection.php line 39:
                                                    
    SQLSTATE[HY000] [2002] No such file or directory  
                                                    

    make:migration

    error sending signal urgent I/O condition os: process already finished
    exit status 1
</pre>

_Descripcion **Solucion**:_ error consiste en que la variable de entorno **DATABASE_URL** que leida por _bundle-migrations del orm_ estaba mal seteada, se setea con los parametro correctos y fin del problema.

<pre>
    bash-5.1# echo $DATABASE_URL
    output: "postgresql://ramon:admin@127.0.0.1:8091/symfony?serverVersion=13.2
</pre>

<pre>
    bash-5.1# export DATABASE_URL="postgresql://root:admin@172.20.0.2:5432/symfony?serverVersion=13&charset=utf8"
</pre>
##### < Migracion de BD

##### > Error en conexion Doctrine
_Descripcion **Error**:_
_Surge_ al instalar la extension de symfony [**Doctrine**](https://symfony.com/doc/current/the-fast-track/es/8-doctrine.html) 
<pre> symfony composer req "orm:^2" </pre> 
Al actualizar la pagina lanza el siguiente error: 

<pre>
An exception occurred in driver: SQLSTATE[HY000] [2002] No such file or directory

Doctrine\DBAL\Exception\ConnectionException:
An exception occurred in driver: SQLSTATE[HY000] [2002] No such file or directory
#

at Doctrine\DBAL\Driver\PDOMySql\Driver->connect(array('url' => '"postgresql://root:admin@docker_symfony-db:5432/symfony?serverVersion=13&charset=utf8"', 'driver' => 'pdo_mysql', 'host' => 'localhost', 'port' => null, 'user' => 'root', 'password' => null, 'driverOptions' => array(), 'defaultTableOptions' => array('collate' => 'utf8mb4_unicode_ci'), 'dbname' => 'postgresql://root:admin@docker_symfony-db:5432/symfony', 'serverVersion' => '13', 'charset' => 'utf8mb4'), 'root', null, array())

#
</pre>


_Descripcion **Solucion**:_
1. **investigacion** surgio que el error estaba en las _variables de entorno_ que le pasaba al servidor web el que luego, con ellas, creaba la conexion a la BD usando **orm** de [_Doctrine_](https://symfony.com/doc/current/the-fast-track/es/8-doctrine.html)

2. **correcciones** se realizaron en el archivo docker-compose.yml en la seccion de creacion del servidor php-web:
<pre>
   php-web:
    # ...
        environment:
            - PGHOST=docker_${PROYECT_NAME}-db
            - PGPORT=5432
            - PGDATABASE=${DB_NAME}
            - PGUSER=${DB_USER}
            - PGPASSWORD=${DB_PASSWORD}
            - DATABASE_URL="postgresql://${DB_USER}:${DB_PASSWORD}@docker_${PROYECT_NAME}-db:5432/${DB_NAME}?serverVersion=13&charset=utf8"
    ... #
</pre>

se pasaron variables de entorno **PG*** las que son tomadas por _doctrine_ al momento de crear la conexion a la bd con **orm**.

La variable de entorno _**DATABASE_URL**_ no se pasa porque genera conflicto, debido a que doctrine la toma para crear la conexion, ingnorando las variable **PG***. 

<pre>
   php-web:
    # ...
        environment:
            - PGHOST=docker_${PROYECT_NAME}-db
            - PGPORT=5432
            - PGDATABASE=${DB_NAME}
            - PGUSER=${DB_USER}
            - PGPASSWORD=${DB_PASSWORD}
    ... #
</pre>
##### < Error en conexion Doctrine

##### > Error al levantar el entorno 
_Descripcion **Problema**:_ Luego de intentar levantar el entorno de desarrollo lanza el siguiente error:
    <pre>
        docker-compose up

        Starting docker_symfony-db ... error

        ERROR: for docker_symfony-db  Cannot start service      db_postgres: failed to create endpoint  docker_symfony-db     on network docker_default:     failed to add the host   (vethf4ec776) <=> sandbox  (veth2efff87) pair interfaces:  operation not supported

        ERROR: for db_postgres  Cannot start service    db_postgres:   failed to create endpoint   docker_symfony-db on network    docker_default: failed    to add the host (vethf4ec776) <=>   sandbox    (veth2efff87) pair interfaces: operation not  supported
        ERROR: Encountered errors while bringing up the     project.
    </pre>

_Descripcion **Solucion**:_ De la investigacion, surgio que el problme puede haber surgido luego de actualizar la pc y no reiniciar. Se reinicia y levanto el entorno correctamente. 
##### < Error al levantar el entorno 

##### > Borrado de var/cache/dev
_Descripcion **Problema**:_ En paso21 se purgo la el directorio cache, eliminado al directorio dev.
Esto trajo un error al renderizar los archivos *.html.twig, en particualar la funcion path.

_Descripcion **Solucion**:_ Se elimino el directorio */cache* haciendo que al levantar el servidor se creara un nuevo directorio /cache/dev y asi solucionando el problema
##### < Borrado de var/cache/dev 

##### > command "yarn encore dev" failed: exit status 1 "@popperjs/core"
_Descripcion **Problema**:_ Al ejecutar el compilador de css _symfony run yarn encore dev_ da un error: 
    <pre>
        ERROR  Failed to compile with 1 errors                   5:38:44 PM

        Module build failed: Module not found:
        "./node_modules/bootstrap/dist/js/bootstrap.esm.js" contains a reference to the file "@popperjs/core".
        
        This file can not be found, please check it for typos or update it if the file got moved.
</pre>

_Descripcion **Solucion**:_ De la investigacion no sugiere la instalacion de la dependencia @popperjs/core, lo que se hace [*yarn add @popperjs/core*](https://www.npmjs.com/package/@popperjs/core), y se soluciona el problema.
##### < command "yarn encore dev" failed: exit status 1 "@popperjs/core"

##### > fetch() en webServer, api SPA
_Descripcion **Problema**:_ En paso 27.6 se hace despliegue de servidor web webpack para pagina SPA. La app no hacia la peticion web a la apiWeb. Por otro lado al compilar los archivos con webpack _$ API_ENDPOINT=`symfony var:export SYMFONY_PROJECT_DEFAULT_ROUTE_URL --dir=..` yarn encore dev_ me lanzaba un error que no encontraba la variable de entorno SYMFONY. 
_Descripcion **Solucion**:_ *1. Variable de Entorno:* Se setea la vaiable de entorno en docker-compose. *2. Fetch():* La url debe completa _http://localhost:8100/_
##### < fetch() en webServer, api SPA 

#### > Redis conection ###
**Video de Apoyo**
    1. [_Redis_](https://www.youtube.com/watch?v=g-VBbGtG6nQ)

_Descripcion **Problema**:_ 
    1. En paso 31, se configura redis como cache de sesiones. Al actualizar la app no renderiza y lanza un error relacionado con la variable de entorno REDIS_URL.
    2. Solucionado _punto 1_, el proyecto no levanta, solicitando el paquete **predis/predis**

_Descripcion **Solucion**:_ 
    1. Al igual que el problema con DB_URL se setea la variable de entorno en el archivo __*.env*__ del _proyecto symfony_ debiendo quedar la url conformada completamente: 
        <pre>
            redis://ip_servidor_redis:puerto
            REDIS_URL=redis://ip_servidor_redis:puerto
        </pre>
    2. De lo investigado surge que una dependencia que se instala con composer:
        <pre>
            symfony composer req predis/predis
        </pre>
#### < Redis conection ###