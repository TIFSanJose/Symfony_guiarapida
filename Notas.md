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
