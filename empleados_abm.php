<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    exit("Sesión expirada");
}

$empresaId = $_SESSION["EmpresaId"];
$accion = $_POST['accion'] ?? 'guardar';

try {
    if ($accion == "eliminar") {
        $id = $_POST["EmpleadoId"];
        $stmt = $pdo->prepare("DELETE FROM Empleados WHERE EmpleadoId=? AND EmpresaId=?");
        $stmt->execute([$id, $empresaId]);
        echo "ok";
    } else {
        $id = $_POST["EmpleadoId"] ?? null;
        $empleado = $_POST["Empleado"];
        $categoria = $_POST["Categoria"] ?? null;
        $valorHora = $_POST["ValorHora"] ?? null;
        $jornal = $_POST["Jornal"];
        $habilitado = $_POST["Habilitado"];

        if ($id) {
            $stmt = $pdo->prepare("UPDATE Empleados SET Empleado=?, Categoria=?, ValorHora=?, Jornal=?, Habilitado=? WHERE EmpleadoId=? AND EmpresaId=?");
            $stmt->execute([$empleado, $categoria, $valorHora, $jornal, $habilitado, $id, $empresaId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO Empleados (EmpresaId, Empleado, Categoria, ValorHora, Jornal, Habilitado) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$empresaId, $empleado, $categoria, $valorHora, $jornal, $habilitado]);
        }
        echo "ok";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
