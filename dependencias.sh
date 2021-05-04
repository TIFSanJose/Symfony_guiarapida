#Dependencias de symfony
/bin/bash!

##############################################################################
#    Componentes Symfony: Paquetes que implementan las características       #
#    principales y abstracciones de bajo nivel que la mayoría de las         #
#    aplicaciones necesitan (enrutamiento, consola, cliente HTTP, mailer,    #
#    caché, etc.);                                                           #
#                                                                            #   
#    Bundles Symfony: Paquetes que añaden características de alto nivel o    #
#    proporcionan integraciones con librerías de terceros (los bundles son,  #
#    en su mayoría, aportados por la comunidad).                             #   
##############################################################################

#componente Symfony Profiler
symfony composer req profiler --dev

# herramientas que nos ayuden a investigar los problemas en el desarrollo, pero también los de producción:
symfony composer req logger

#herramientas de depuración, vamos a instalarlas sólo en desarrollo:
symfony composer req debug --dev

#generar controladores sin esfuerzo, podemos usar el paquete symfony/maker-bundle
#Maker bundle te ayuda a generar muchas clases diferentes. Lo usaremos todo el tiempo en este libro. 
#Cada «generador» se define en un comando y todos los comandos forman parte del espacio de nombres del comando make.
symfony composer req maker --dev

#Symfony soporta YAML, XML, PHP y anotaciones desde el primer momento.
#Para usar anotaciones, necesitamos añadir otra dependencia:
symfony composer req annotations

#vamos a hacer uso de Doctrine,
#Este comando instala algunas dependencias: Doctrine DBAL (una capa de abstracción de base de datos), 
#Doctrine ORM (una biblioteca para manipular el contenido de nuestra base de datos usando objetos PHP), y Doctrine Migrations.
symfony composer req "orm:^2"
#  * Modify your DATABASE_URL config in .env

#  * Configure the driver (postgresql) and server_version (13) in config/packages/doctrine.yaml

#EasyAdmin genera automáticamente un área de administración para tu aplicación basada en controladores específicos
symfony composer req "admin:^3"

#Instalando Twig
symfony composer req twig

#
symfony composer req "twig/intl-extra:^3"

#Debido a que utilizamos un validador para garantizar la unicidad de los slugs, necesitamos agregar el 
#componente Symfony Validator:
symfony composer req validator

#componente de Symfony String, que facilita la manipulación de las cadenas y proporciona un slugger:
symfony composer req string

#La restricción de la imagen funciona comprobando el tipo mime; se requiere el componente Mime para que funcione:
symfony composer req mime

#Para hacer llamadas a la API, utiliza el componente HttpClient de Symfony:
symfony composer req http-client

#Symfony se basa en PHPUnit para las pruebas unitarias (unit tests). Vamos a instalarlo:
symfony composer req phpunit --dev

#output
#[UnexpectedValueException]                                 
#  "phpunit" is not a valid alias. Did you mean this:         
#    "symfony/phpunit-bridge", supported aliases: "phpunit-b  
#  ridge", "phpunitbridge"  

#https://symfony.com/doc/current/testing.html
symfony composer require --dev phpunit/phpunit symfony/test-pack

symfony composer require symfony/panther --dev

#instala el bundle Doctrine Fixtures:
symfony composer req orm-fixtures --dev

#output
#[UnexpectedValueException]            
# "orm-fixtures" is not a valid alias.

#https://packagist.org/packages/doctrine/data-fixtures
doctrine/coding-standard

symfony composer req --dev doctrine/dbal:^2.5.4

symfony composer req --dev doctrine/common

symfony composer req --dev doctrine/persistence

symfony composer req --dev doctrine/data-fixtures

#Para restablecer la base de datos entre pruebas, instala DoctrineTestBundle:
symfony composer req "dama/doctrine-test-bundle:^6" --dev

#puedes usar un navegador real y la capa HTTP real gracias a Symfony Panther:
symfony composer req panther --dev

#El componente Messenger es el encargado de la gestión de código asíncrono cuando usamos Symfony
symfony composer req messenger

#componente Workflow de Symfony
symfony composer req workflow

#componente Symfony Mailer
symfony composer req mailer
