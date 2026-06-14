<?php

include("conexion.php");

$id = $_GET['id'];

$sql = "
DELETE FROM productos
WHERE pro_id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->execute([$id]);

header("Location: productos.php?mensaje=eliminado");
exit();

?>