<?php

require('pdf/fpdf.php');
include("conexion.php");

/* CONSULTA PRODUCTOS */

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

/* ESTADISTICAS */

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

/* PDF */

$pdf = new FPDF();
$pdf->AddPage();

/* LOGO */

$pdf->Image('img/logo_tienda.png',10,5,25);

/* TITULO */

$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,15,'REPORTE DE INVENTARIO',0,1,'C');

$pdf->Ln(5);

$pdf->SetFont('Arial','',12);

$pdf->Cell(0,8,'Autor: Guillermo Vallejo',0,1);
$pdf->Cell(0,8,'Fecha: '.date('d/m/Y'),0,1);

$pdf->Ln(5);

/* ENCABEZADO TABLA */

$pdf->SetFont('Arial','B',11);

$pdf->Cell(60,10,'Descripcion',1,0,'C');
$pdf->Cell(35,10,'Categoria',1,0,'C');
$pdf->Cell(35,10,'Marca',1,0,'C');
$pdf->Cell(25,10,'Precio',1,0,'C');
$pdf->Cell(20,10,'Stock',1,1,'C');

/* DATOS */

$pdf->SetFont('Arial','',10);

while($fila = $resultado->fetch(PDO::FETCH_ASSOC))
{

    $pdf->Cell(
        60,
        8,
        utf8_decode($fila['pro_descripcion']),
        1
    );

    $pdf->Cell(
        35,
        8,
        utf8_decode($fila['cat_nombre']),
        1
    );

    $pdf->Cell(
        35,
        8,
        utf8_decode($fila['mar_nombre']),
        1
    );

    $pdf->Cell(
        25,
        8,
        '$'.number_format($fila['pro_precio_v'],2),
        1
    );

    $pdf->Cell(
        20,
        8,
        $fila['pro_stock'],
        1
    );

    $pdf->Ln();

}

/* SEGUNDA PAGINA */

$pdf->AddPage();

/* LOGO */

$pdf->Image('img/logo_tienda.png',10,10,25);

$pdf->SetFont('Arial','B',18);

$pdf->Cell(
    0,
    15,
    'RESUMEN DEL INVENTARIO',
    0,
    1,
    'C'
);

$pdf->Ln(10);

$pdf->SetFont('Arial','B',14);

$pdf->Cell(90,12,'Total Productos',1);
$pdf->Cell(40,12,$totalProductos,1);
$pdf->Ln();

$pdf->Cell(90,12,'Productos Stock Bajo',1);
$pdf->Cell(40,12,$stockBajo,1);
$pdf->Ln();

$pdf->Cell(90,12,'Productos con IVA',1);
$pdf->Cell(40,12,$conIVA,1);
$pdf->Ln();

$pdf->Ln(15);

$pdf->SetFont('Arial','I',12);

$pdf->Cell(
    0,
    10,
    'TechStore Multicategoria - Sistema de Gestion de Inventario',
    0,
    1,
    'C'
);

$pdf->Output();

?>