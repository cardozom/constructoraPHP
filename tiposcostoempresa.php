<?php
session_start();
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}
$empresaId = $_SESSION["EmpresaId"];
require_once "conexion.php";

// Procesamiento de AJAX
if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "listar":
            $page = isset($_POST["page"]) ? (int)$_POST["page"] : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM TiposCostoEmpresa WHERE EmpresaId=?");
            $stmt->execute([$empresaId]);
            $total = $stmt->fetchColumn();
            $pages = ceil($total / $limit);

            $stmt = $pdo->prepare("SELECT * FROM TiposCostoEmpresa WHERE EmpresaId=? ORDER BY CostoId DESC LIMIT $limit OFFSET $offset");
            $stmt->execute([$empresaId]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["data"=>$data,"pages"=>$pages,"current"=>$page]);
            exit;
        case "agregar":
            $costo = trim($_POST["Costo"]);
            if ($costo == "") { echo "error"; exit; }
            $stmt = $pdo->prepare("INSERT INTO TiposCostoEmpresa (EmpresaId, Costo) VALUES (?, ?)");
            $stmt->execute([$empresaId, $costo]);
            echo "ok";
            exit;
        case "editar":
            $id = $_POST["CostoId"];
            $costo = trim($_POST["Costo"]);
            $stmt = $pdo->prepare("UPDATE TiposCostoEmpresa SET Costo=? WHERE CostoId=? AND EmpresaId=?");
            $stmt->execute([$costo, $id, $empresaId]);
            echo "ok";
            exit;
        case "eliminar":
            $id = $_POST["CostoId"];
            $stmt = $pdo->prepare("DELETE FROM TiposCostoEmpresa WHERE CostoId=? AND EmpresaId=?");
            $stmt->execute([$id, $empresaId]);
            echo "ok";
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Costo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container mt-4">
    <h3>Tipos de Costo de la Empresa</h3>

    <form id="formCosto" autocomplete="off" class="mb-3">
        <input type="hidden" name="CostoId" id="CostoId">
        <div class="row">
            <div class="col-md-6">
                <input type="text" maxlength="50" name="Costo" id="Costo" class="form-control" placeholder="Nombre del costo" required autocomplete="off">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Costo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaDatos"></tbody>
    </table>
    <nav>
        <ul class="pagination" id="paginacion"></ul>
    </nav>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
    let paginaActual = 1;

    function listar(page=1){
        $.post("tiposcostoempresa.php",{action:"listar",page:page},function(res){
            let r = JSON.parse(res);
            let html="";
            r.data.forEach(d=>{
                html+=`<tr>
                    <td>${d.CostoId}</td>
                    <td>${d.Costo}</td>
                    <td>
                        <button class='btn btn-warning btn-sm' onclick='editar(${d.CostoId},"${d.Costo}")'>Editar</button>
                        <button class='btn btn-danger btn-sm' onclick='eliminar(${d.CostoId})'>Eliminar</button>
                    </td>
                </tr>`;
            });
            $("#tablaDatos").html(html);

            // paginación
            let pag="";
            for(let i=1;i<=r.pages;i++){
                pag+=`<li class="page-item ${i==r.current?"active":""}">
                    <a class="page-link" href="#" onclick="listar(${i})">${i}</a>
                </li>`;
            }
            $("#paginacion").html(pag);
            paginaActual = r.current;
        });
    }

    $("#formCosto").on("submit",function(e){
        e.preventDefault();
        let datos = $(this).serialize();
        let action = $("#CostoId").val()==""?"agregar":"editar";
        $.post("tiposcostoempresa.php",datos+"&action="+action,function(res){
            if(res=="ok"){
                alertify.success("Guardado correctamente");
                $("#formCosto")[0].reset();
                $("#CostoId").val("");
                listar(paginaActual);
            } else {
                alertify.error("Error al guardar");
            }
        });
    });

    $("#btnCancelar").click(function(){
        $("#formCosto")[0].reset();
        $("#CostoId").val("");
    });

    function editar(id,costo){
        $("#CostoId").val(id);
        $("#Costo").val(costo);
    }

    function eliminar(id){
        alertify.confirm("Eliminar","¿Seguro de eliminar este registro?",
            function(){
                $.post("tiposcostoempresa.php",{action:"eliminar",CostoId:id},function(res){
                    if(res=="ok"){
                        alertify.success("Eliminado");
                        listar(paginaActual);
                    } else {
                        alertify.error("Error al eliminar");
                    }
                });
            },
            function(){}
        );
    }

    $(document).ready(function(){ listar(); });
    </script>
</body>
</html>
