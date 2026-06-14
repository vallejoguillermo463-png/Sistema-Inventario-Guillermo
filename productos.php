<?php

include("conexion.php");

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

/* Estadísticas */

$totalProductos = $conexion->query("
SELECT COUNT(*) FROM productos
")->fetchColumn();

$stockBajo = $conexion->query("
SELECT COUNT(*) FROM productos
WHERE pro_stock <= pro_stock_min
")->fetchColumn();

$conIVA = $conexion->query("
SELECT COUNT(*) FROM productos
WHERE pro_paga_iva = 1
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>TechStore Multicategoría</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
    background:#e9ecef;
}

td{
    vertical-align:middle !important;
}

table.dataTable{
    border-radius:10px;
    overflow:hidden;
}

</style>

</head>

<body>

<div class="container mt-4">

<div class="card shadow-lg">

<div class="card-header text-white"
style="background:#0d47a1;">

<div style="display:flex;align-items:center;">

<img
src="img/logo_tienda.png"
width="60"
height="60"
style="margin-right:15px;">

<div>

<h2 style="margin:0;">
TechStore Multicategoría
</h2>

<p style="
margin:0;
font-size:20px;
font-weight:bold;
">

Por: Guillermo Vallejo

</p>

<p style="margin:0;">
Fecha:
<?php echo date('d/m/Y'); ?>
</p>

</div>

<div style="margin-left:auto;">

<a
href="reporte_pdf.php"
target="_blank"
class="btn btn-danger btn-lg me-2">

📄 Exportar PDF

</a>

<a
href="nuevo_producto.php"
class="btn btn-light btn-lg">

➕ Nuevo Producto

</a>

</div>

</div>

</div>

<div class="card-body">

<?php

if(isset($_GET['mensaje']))
{

if($_GET['mensaje']=="guardado")
{
echo '

<div class="alert alert-success alert-dismissible fade show">

✅ Producto registrado correctamente.

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

';
}

if($_GET['mensaje']=="actualizado")
{
echo '

<div class="alert alert-primary alert-dismissible fade show">

✏️ Producto actualizado correctamente.

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

';
}

if($_GET['mensaje']=="eliminado")
{
echo '

<div class="alert alert-danger alert-dismissible fade show">

🗑️ Producto eliminado correctamente.

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

';
}

}

?>

<div class="row mb-3">

<div class="col-md-4">

<div class="alert alert-primary">

📦 Total Productos:

<strong>

<?php echo $totalProductos; ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="alert alert-danger">

⚠️ Stock Bajo:

<strong>

<?php echo $stockBajo; ?>

</strong>

</div>

</div>

<div class="col-md-4">

<div class="alert alert-success">

💰 Con IVA:

<strong>

<?php echo $conIVA; ?>

</strong>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Productos por Categoría

</div>

<div class="card-body">

<canvas id="graficoCategorias"
height="100"></canvas>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

Estado del Inventario

</div>

<div class="card-body">

<canvas
id="graficoStock"
style="height:250px;">
</canvas>

</div>

</div>

</div>

</div>

<table
id="tablaProductos"
class="table table-striped table-hover mt-4">

<thead>

<tr>

<th>Imagen</th>
<th>Descripción</th>
<th>Categoría</th>
<th>Marca</th>
<th>Precio Venta</th>
<th>Stock</th>
<th>Estado</th>
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
class="rounded shadow"
style="object-fit:cover;">

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

if($fila['pro_stock'] <= $fila['pro_stock_min'])
{
    echo '
    <span class="badge bg-danger">
    🔴 Stock Bajo
    </span>';
}
else
{
    echo '
    <span class="badge bg-success">
    🟢 Normal
    </span>';
}

?>

</td>

<td>

<?php

if($fila['pro_paga_iva']==1)
{
    echo '
    <span class="badge bg-success">
    Sí
    </span>';
}
else
{
    echo '
    <span class="badge bg-secondary">
    No
    </span>';
}

?>

</td>

<td>

<a
href="editar_producto.php?id=<?php echo $fila['pro_id']; ?>"
class="btn btn-warning btn-sm">

✏️ Editar

</a>

<a
href="eliminar.php?id=<?php echo $fila['pro_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Desea eliminar este producto?');">

🗑 Eliminar

</a>

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

<?php

$tecnologia = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE cat_id = 1
")->fetchColumn();

$oficina = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE cat_id = 2
")->fetchColumn();

$hogar = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE cat_id = 3
")->fetchColumn();

$deportes = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE cat_id = 4
")->fetchColumn();

$mascotas = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE cat_id = 5
")->fetchColumn();

$normal = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE pro_stock > pro_stock_min
")->fetchColumn();

$bajo = $conexion->query("
SELECT COUNT(*)
FROM productos
WHERE pro_stock <= pro_stock_min
")->fetchColumn();

?>

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

<script>

const ctx1 =
document.getElementById('graficoCategorias');

new Chart(ctx1, {

type:'bar',

data:{

labels:[

'Tecnología',
'Oficina',
'Hogar',
'Deportes',
'Mascotas'

],

datasets:[{

label:'Productos',

data:[

<?php echo $tecnologia; ?>,
<?php echo $oficina; ?>,
<?php echo $hogar; ?>,
<?php echo $deportes; ?>,
<?php echo $mascotas; ?>

]

}]

}

});

</script>

<script>

const ctx2 =
document.getElementById('graficoStock');

new Chart(ctx2, {

type:'pie',

data:{

labels:[

'Normal',
'Stock Bajo'

],

datasets:[{

data:[

<?php echo $normal; ?>,
<?php echo $bajo; ?>

]

}]

},

options:{

responsive:true,
maintainAspectRatio:false

}

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>