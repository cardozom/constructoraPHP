<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "conexion.php";

// Si no existe sesión, redirige (solo por seguridad)
if (!isset($_SESSION["EmpresaId"])) {
    die("<h3>No existe sesión iniciada</h3>");
}

$empresaId = $_SESSION["EmpresaId"];

// ALTA
if (isset($_POST["accion"]) && $_POST["accion"] == "insertar") {
    $stmt = $pdo->prepare("INSERT INTO TiposCostoObra (EmpresaId, Costo, TipoCosto) VALUES (?, ?, ?)");
    $stmt->execute([$empresaId, $_POST["Costo"], $_POST["TipoCosto"]]);
    header("Location: tiposcostoobra.php");
    exit;
}

// MODIFICAR
if (isset($_POST["accion"]) && $_POST["accion"] == "editar") {
    $stmt = $pdo->prepare("UPDATE TiposCostoObra SET Costo=?, TipoCosto=? WHERE CostoId=? AND EmpresaId=?");
    $stmt->execute([$_POST["Costo"], $_POST["TipoCosto"], $_POST["CostoId"], $empresaId]);
    header("Location: tiposcostoobra.php");
    exit;
}

// ELIMINAR
if (isset($_GET["eliminar"])) {
    $stmt = $pdo->prepare("DELETE FROM TiposCostoObra WHERE CostoId=? AND EmpresaId=?");
    $stmt->execute([$_GET["eliminar"], $empresaId]);
    header("Location: tiposcostoobra.php");
    exit;
}

// CONSULTA PARA LISTAR
$stmt = $pdo->prepare("SELECT * FROM TiposCostoObra WHERE EmpresaId=? ORDER BY CostoId DESC");
$stmt->execute([$empresaId]);
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tipos de Costo Obra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
</head>
<body class="container p-4">

<h2>Tipos de Costo por Obra</h2>

<!-- FORMULARIO -->
<form method="POST" class="row g-3 mb-4">
    <input type="hidden" name="accion" value="insertar">
    <div class="col-md-5">
        <input type="text" name="Costo" class="form-control" placeholder="Costo" required>
    </div>
    <div class="col-md-5">
        <input type="text" name="TipoCosto" class="form-control" placeholder="Tipo de Costo" required>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">Agregar</button>
    </div>
</form>

<!-- LISTADO -->
<table class="table table-bordered">
    <tr class="table-secondary">
        <th>ID</th>
        <th>Costo</th>
        <th>Tipo Costo</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($lista as $row): ?>
        <tr>
            <td><?= $row["CostoId"] ?></td>
            <td><?= $row["Costo"] ?></td>
            <td><?= $row["TipoCosto"] ?></td>
            <td>
                <a href="tiposcostoobra_editar.php?id=<?= $row['CostoId'] ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="tiposcostoobra.php?eliminar=<?= $row['CostoId'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('¿Eliminar el registro?')">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
