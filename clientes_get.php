<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    exit("Sesión expirada");
}

$id = $_GET["ClienteId"];
$empresaId = $_SESSION["EmpresaId"];

$stmt = $pdo->prepare("SELECT * FROM Clientes WHERE ClienteId=? AND EmpresaId=?");
$stmt->execute([$id, $empresaId]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
