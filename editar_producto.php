<?php

include("conexion.php");

$id = $_GET['id'];

$sql = "SELECT * FROM productos WHERE pro_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$producto = $stmt->fetch(PDO::FETCH_ASSOC);
$categorias = $conexion->query("SELECT * FROM categorias");
$marcas = $conexion->query("SELECT * FROM marcas");

if(isset($_POST['actualizar']))
{
    $descripcion = $_POST['descripcion'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];
    $imagen = $producto['pro_imagen'];

    if($_FILES['imagen']['name'] != "")
    {
        $imagen = $_FILES['imagen']['name'];

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            "img/" . $imagen
        );
    }

    $sqlUpdate = "
    UPDATE productos
    SET
        pro_descripcion = ?,
        pro_stock = ?,
        pro_precio_v = ?,
        pro_imagen = ?,
        cat_id = ?,
        mar_id = ?
    WHERE pro_id = ?
    ";

    $stmtUpdate = $conexion->prepare($sqlUpdate);

    $stmtUpdate->execute([
        $descripcion,
        $stock,
        $precio,
        $imagen,
        $categoria,
        $marca,
        $id
    ]);

    header("Location: productos.php?mensaje=actualizado");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="UTF-8">
<title>Editar Producto - TechStore</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#e9ecef;">

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header text-white"
        style="background:#0d47a1;">

            <h3>Editar Producto - TechStore</h3>

        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Descripción</label>

                    <input
                        type="text"
                        name="descripcion"
                        class="form-control"
                        value="<?php echo $producto['pro_descripcion']; ?>"
                        required>
                </div>

                <div class="mb-3">

                    <label>Categoría</label>

                    <select name="categoria" class="form-control">

                    <?php
                    while($cat = $categorias->fetch(PDO::FETCH_ASSOC))
                    {
                    ?>

                    <option
                    value="<?php echo $cat['cat_id']; ?>"
                    <?php
                    if($cat['cat_id']==$producto['cat_id'])
                    {
                        echo "selected";
                    }
                    ?>
                    >

                    <?php echo $cat['cat_nombre']; ?>

                    </option>

                    <?php
                    }
                    ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label>Marca</label>

                    <select name="marca" class="form-control">

                    <?php
                    while($mar = $marcas->fetch(PDO::FETCH_ASSOC))
                    {
                    ?>

                    <option
                    value="<?php echo $mar['mar_id']; ?>"
                    <?php
                    if($mar['mar_id']==$producto['mar_id'])
                    {
                        echo "selected";
                    }
                    ?>
                    >

                    <?php echo $mar['mar_nombre']; ?>

                    </option>

                    <?php
                    }
                    ?>

                    </select>

                </div>

                <div class="mb-3">
                    <label>Stock</label>

                    <input
                        type="number"
                        name="stock"
                        class="form-control"
                        value="<?php echo $producto['pro_stock']; ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label>Precio Venta</label>

                    <input
                        type="number"
                        step="0.01"
                        name="precio"
                        class="form-control"
                        value="<?php echo $producto['pro_precio_v']; ?>"
                        required>
                </div>

                <div class="mb-3">

                    <label>Imagen Actual</label>

                    <br>

                    <img
                    src="img/<?php echo $producto['pro_imagen']; ?>"
                    width="120"
                    class="img-thumbnail">

                </div>

                <div class="mb-3">

                    <label>Nueva Imagen</label>

                    <input
                    type="file"
                    name="imagen"
                    class="form-control">

                </div>

                <button
                    type="submit"
                    name="actualizar"
                    class="btn btn-success">

                    Actualizar Producto

                </button>

                <a
                    href="productos.php"
                    class="btn btn-secondary">

                    Volver

                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>