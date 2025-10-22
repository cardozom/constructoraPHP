<?php
session_start();
require "conexion.php";

$usuario = $_POST["usuario"] ?? "";
$clave   = $_POST["clave"] ?? "";

$sql = "SELECT us.UsuarioId, us.Usuario, us.EmpresaId, emp.Empresa, us.Perfil 
        FROM usuarios us
        INNER JOIN empresaconstructora emp ON us.EmpresaId = emp.EmpresaId
        WHERE us.Usuario = :usuario AND us.Clave = :clave";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":usuario" => $usuario,
    ":clave"   => $clave
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION["UsuarioId"] = $user["UsuarioId"];
    $_SESSION["Usuario"]   = $user["Usuario"];
    $_SESSION["EmpresaId"] = $user["EmpresaId"];
    $_SESSION["Empresa"]   = $user["Empresa"];
    $_SESSION["Perfil"]    = $user["Perfil"];
    header("Location: inicio.php");
    exit;
} else {
    header("Location: index.php?error=1");
    exit;
}
?>
