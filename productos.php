<?php

$host = "localhost";
$dbname = "db_tienda";
$user = "root";
$pass = "root";

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}

$sql = "
SELECT
p.*,
c.cat_nombre,
m.mar_nombre
FROM productos p
INNER JOIN categorias c
ON p.cat_id = c.cat_id
INNER JOIN marcas m
ON p.mar_id = m.mar_id
";

$resultado = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Catálogo de Productos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<style>

body{
    background:#e9ecef;
}

.card{
    border:none;
}

.card-header{
    background:#0d47a1;
    color:white;
}

table.dataTable tbody tr:hover{
    background:#f5f5f5;
}

</style>

</head>

<body>

<div class="container mt-4 mb-4">

<div class="card shadow-lg">

<div class="card-header">

<div class="d-flex justify-content-between align-items-center">

<div style="display:flex;align-items:center;">

<img src="img/logo_tienda.png"
    width="70"
    style="margin-right:15px;">

<div>

<h1 style="margin:0;">
TechStore Multicategoría
</h1>

<p style="
margin:0;
font-size:22px;
font-weight:bold;
color:white;
">
Por: Guillermo Vallejo
</p>

</div>

</div>

<button class="btn btn-light btn-lg">
➕ Nuevo Producto
</button>

</div>

</div>

<div class="card-body">

<table id="tablaProductos" class="display">

<thead>

<tr>

<th>Imagen</th>
<th>Descripción</th>
<th>Categoría</th>
<th>Marca</th>
<th>Precio Venta</th>
<th>Stock</th>
<th>IVA</th>
<th>Acciones</th>

</tr>

</thead>

<tbody>

<?php

while($fila = $resultado->fetch(PDO::FETCH_ASSOC))
{

?>

<tr>

<td>

<img
src="img/<?php echo $fila['pro_imagen']; ?>"
width="70"
height="70"
style="
object-fit:cover;
border-radius:10px;
">

</td>

<td>
<?php echo $fila['pro_descripcion']; ?>
</td>

<td>
<?php echo $fila['cat_nombre']; ?>
</td>

<td>
<?php echo $fila['mar_nombre']; ?>
</td>

<td>
$<?php echo number_format($fila['pro_precio_v'],2); ?>
</td>

<td>
<?php echo $fila['pro_stock']; ?>
</td>

<td>

<?php

if($fila['pro_paga_iva']==1)
{
    echo '<span class="badge bg-success">SI</span>';
}
else
{
    echo '<span class="badge bg-danger">NO</span>';
}

?>

</td>

<td>

<button class="btn btn-warning btn-sm">
✏ Editar
</button>

<button class="btn btn-danger btn-sm">
🗑 Eliminar
</button>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

<script>

$(document).ready(function(){

$('#tablaProductos').DataTable({

    pageLength:10,
    language:{

        "lengthMenu":"Mostrar _MENU_ registros",
        "zeroRecords":"No se encontraron registros",
        "info":"Mostrando _START_ a _END_ de _TOTAL_ registros",
        "infoEmpty":"Sin registros",
        "search":"Buscar:",
        "paginate":{
            "first":"Primero",
            "last":"Último",
            "next":"Siguiente",
            "previous":"Anterior"
        }

    }

});

});

</script>

</body>
</html>