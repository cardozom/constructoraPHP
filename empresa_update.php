<?php
session_start();
require_once "conexion.php";

// Verificar sesión
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    echo "No autorizado";
    exit();
}

if (isset($_POST["EmpresaId"], $_POST["Saldo"], $_POST["FechaInicioEj"])) {
    try {
        $stmt = $pdo->prepare("UPDATE EmpresaConstructora 
                               SET Saldo = ?, FechaInicioEj = ? 
                               WHERE EmpresaId = ?");
        $ok = $stmt->execute([$_POST["Saldo"], $_POST["FechaInicioEj"], $_POST["EmpresaId"]]);

        echo $ok ? "ok" : "error";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else {
    echo "Datos incompletos";
}
