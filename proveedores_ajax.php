<?php
session_start();
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    die("Sesión expirada");
}
$empresaId = $_SESSION["EmpresaId"];
require_once("conexion.php");

$accion = $_REQUEST["accion"] ?? "";

if($accion=="listar"){
    $pagina = $_GET["pagina"] ?? 1;
    $limite = 10;
    $offset = ($pagina-1)*$limite;

    $total = $pdo->prepare("SELECT COUNT(*) FROM Proveedores WHERE EmpresaId=?");
    $total->execute([$empresaId]);
    $totalReg = $total->fetchColumn();

    $stmt = $pdo->prepare("SELECT p.*, i.CondicionIva 
                           FROM Proveedores p 
                           JOIN tblCondicionIva i ON p.CondicionIvaId=i.CondicionIvaId
                           WHERE EmpresaId=? 
                           ORDER BY Proveedor LIMIT $limite OFFSET $offset");
    $stmt->execute([$empresaId]);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table class='table table-striped table-sm'>";
    echo "<thead><tr><th>Proveedor</th><th>CUIT</th><th>Dirección</th><th>Email</th><th>IVA</th><th>Habilitado</th><th>Saldo</th><th>Acciones</th></tr></thead><tbody>";
    foreach($proveedores as $p){
        $json = htmlspecialchars(json_encode($p),ENT_QUOTES,'UTF-8');
        echo "<tr>
                <td>{$p['Proveedor']}</td>
                <td>{$p['CUIT']}</td>
                <td>{$p['Direccion']}</td>
                <td>{$p['email']}</td>
                <td>{$p['CondicionIva']}</td>
                <td>{$p['Habilitado']}</td>
                <td>{$p['Saldo']}</td>
                <td>
                  <button class='btn btn-sm btn-warning' onclick='abrirModal(JSON.parse(`$json`))'>✏️</button>
                  <button class='btn btn-sm btn-danger' onclick='eliminarProveedor({$p['proveedorId']})'>🗑️</button>
                </td>
              </tr>";
    }
    echo "</tbody></table>";

    $paginas = ceil($totalReg/$limite);
    echo "<nav><ul class='pagination'>";
    for($i=1;$i<=$paginas;$i++){
        $active = ($i==$pagina)?"active":"";
        echo "<li class='page-item $active'><a class='page-link' href='#' onclick='cargarProveedores($i)'>$i</a></li>";
    }
    echo "</ul></nav>";
}

if($accion=="guardar"){
    $id = $_POST["proveedorId"] ?? "";
    $data = [
        "EmpresaId"=>$empresaId,
        "Proveedor"=>$_POST["Proveedor"],
        "CondicionIvaId"=>$_POST["CondicionIvaId"],
        "CUIT"=>$_POST["CUIT"],
        "Direccion"=>$_POST["Direccion"],
        "Localidad"=>$_POST["Localidad"],
        "Provincia"=>$_POST["Provincia"],
        "Telefono1"=>$_POST["Telefono1"],
        "email"=>$_POST["email"],
        "Habilitado"=>$_POST["Habilitado"],
        "Saldo"=>$_POST["Saldo"]
    ];

    if($id){
        $sql = "UPDATE Proveedores SET Proveedor=:Proveedor, CondicionIvaId=:CondicionIvaId, CUIT=:CUIT, Direccion=:Direccion,
                Localidad=:Localidad, Provincia=:Provincia, Telefono1=:Telefono1, email=:email, Habilitado=:Habilitado, Saldo=:Saldo
                WHERE proveedorId=:id AND EmpresaId=:EmpresaId";
        $data["id"]=$id;
        $stmt=$pdo->prepare($sql);
        $stmt->execute($data);
        echo "Proveedor actualizado";
    } else {
        $sql = "INSERT INTO Proveedores(EmpresaId,Proveedor,CondicionIvaId,CUIT,Direccion,Localidad,Provincia,Telefono1,email,Habilitado,Saldo)
                VALUES(:EmpresaId,:Proveedor,:CondicionIvaId,:CUIT,:Direccion,:Localidad,:Provincia,:Telefono1,:email,:Habilitado,:Saldo)";
        $stmt=$pdo->prepare($sql);
        $stmt->execute($data);
        echo "Proveedor agregado";
    }
}

if($accion=="eliminar"){
    $id=$_POST["proveedorId"];
    $stmt=$pdo->prepare("DELETE FROM Proveedores WHERE proveedorId=? AND EmpresaId=?");
    $stmt->execute([$id,$empresaId]);
    echo "Proveedor eliminado";
}
