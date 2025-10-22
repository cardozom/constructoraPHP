<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    exit("Sesión expirada");
}

$id = $_GET["EmpleadoId"];
$empresaId = $_SESSION["EmpresaId"];

$stmt = $pdo->prepare("SELECT * FROM Empleados WHERE EmpleadoId=? AND EmpresaId=?");
$stmt->execute([$id, $empresaId]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
