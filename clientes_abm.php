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
        $id = $_POST["ClienteId"];
        $stmt = $pdo->prepare("DELETE FROM Clientes WHERE ClienteId=? AND EmpresaId=?");
        $stmt->execute([$id, $empresaId]);
        echo "ok";
    } else {
        $id = $_POST["ClienteId"] ?? null;
        $cliente = $_POST["Cliente"];
        $condicionIvaId = $_POST["CondicionIvaId"];
        $cuit = $_POST["CUIT"];
        $direccion = $_POST["Direccion"];
        $localidad = $_POST["Localidad"];
        $provincia = $_POST["Provincia"];
        $telefono = $_POST["Telefono1"];
        $email = $_POST["email"];
        $habilitado = $_POST["Habilitado"];
        $saldo = $_POST["Saldo"];

        if ($id) {
            $stmt = $pdo->prepare("UPDATE Clientes 
                SET Cliente=?, CondicionIvaId=?, CUIT=?, Direccion=?, Localidad=?, Provincia=?, Telefono1=?, email=?, Habilitado=?, Saldo=?
                WHERE ClienteId=? AND EmpresaId=?");
            $stmt->execute([$cliente,$condicionIvaId,$cuit,$direccion,$localidad,$provincia,$telefono,$email,$habilitado,$saldo,$id,$empresaId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO Clientes (EmpresaId, Cliente, CondicionIvaId, CUIT, Direccion, Localidad, Provincia, Telefono1, email, Habilitado, Saldo) 
                                   VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$empresaId,$cliente,$condicionIvaId,$cuit,$direccion,$localidad,$provincia,$telefono,$email,$habilitado,$saldo]);
        }
        echo "ok";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
