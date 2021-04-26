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