<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}

$empresaId = $_SESSION["EmpresaId"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Costos Directos de Obra MCC</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container py-4">

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">Costos Directos de Obra</h4>
            <button class="btn btn-light btn-sm" onclick="abrirModal()">➕ Nuevo</button>
        </div>
        <div class="card-body" id="tablaCostos">
            <!-- Aquí carga la tabla con AJAX -->
        </div>
    </div>

    <!-- Modal ABM -->
    <div class="modal fade" id="modalCosto" tabindex="-1">
        <div class="modal-dialog">
            <form id="formCosto" class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Costo Directo de Obra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="CostoId" id="CostoId">
                    <input type="hidden" name="EmpresaId" id="EmpresaId" value="<?= $empresaId ?>">
                    <input type="hidden" name="TipoCosto" id="TipoCosto" value="CD">

                    <div class="mb-3">
                        <label class="form-label">Costo</label>
                        <input type="text" class="form-control" name="Costo" id="Costo" maxlength="50" autocomplete="off" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
    let modal = new bootstrap.Modal(document.getElementById('modalCosto'));

    function cargarTabla(pagina=1) {
        $("#tablaCostos").load("costosdirectosobra_listar.php?pagina=" + pagina);
    }

    function abrirModal(id=null) {
        if (id) {
            $.get("costosdirectosobra_get.php", {CostoId: id}, function(data) {
                let costo = JSON.parse(data);
                $("#CostoId").val(costo.CostoId);
                $("#Costo").val(costo.Costo);
                modal.show();
            });
        } else {
            $("#formCosto")[0].reset();
            $("#CostoId").val('');
            $("#EmpresaId").val('<?= $empresaId ?>');
            $("#TipoCosto").val('CD');
            modal.show();
        }
    }

    $("#formCosto").on("submit", function(e) {
        e.preventDefault();
        $.post("costosdirectosobra_abm.php", $(this).serialize(), function(resp) {
            if (resp=="ok") {
                modal.hide();
                alertify.success("✅ Guardado correctamente");
                cargarTabla();
            } else {
                alertify.error("❌ Error: " + resp);
            }
        });
    });

    function eliminarCosto(id) {
        alertify.confirm("Eliminar", "¿Desea eliminar este costo directo?", 
            function(){
                $.post("costosdirectosobra_abm.php", {accion:"eliminar", CostoId:id}, function(resp){
                    if(resp=="ok"){
                        alertify.success("🗑️ Eliminado");
                        cargarTabla();
                    } else {
                        alertify.error("❌ Error: " + resp);
                    }
                });
            },
            function(){}
        );
    }

    $(document).ready(function(){
        cargarTabla();
    });
    </script>
</body>
</html>
