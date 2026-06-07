<?php

$host = "localhost";
$bd = "db_tienda";
$usuario = "root";
$password = "root";

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$bd;charset=utf8",
        $usuario,
        $password
    );

    echo "Conexión exitosa a la base de datos";

} catch(PDOException $e) {

    echo "Error de conexión: " . $e->getMessage();

}
?>