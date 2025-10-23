<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Empleados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container py-4">

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">Empleados</h4>
            <button class="btn btn-light btn-sm" onclick="abrirModal()">➕ Nuevo</button>
        </div>
        <div class="card-body" id="tablaEmpleados">
            <!-- Aquí carga la tabla con AJAX -->
        </div>
    </div>

    <!-- Modal ABM -->
    <div class="modal fade" id="modalEmpleado" tabindex="-1">
        <div class="modal-dialog">
            <form id="formEmpleado" class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="EmpleadoId" id="EmpleadoId">
                    
                    <div class="mb-3">
                        <label class="form-label">Empleado</label>
                        <input type="text" class="form-control" name="Empleado" id="Empleado" maxlength="50" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" class="form-control" name="Categoria" id="Categoria" maxlength="50" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor Hora</label>
                        <input type="number" step="0.01" class="form-control" name="ValorHora" id="ValorHora" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jornal</label>
                        <input type="number" step="0.01" class="form-control" name="Jornal" id="Jornal" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Habilitado</label>
                        <select class="form-control" name="Habilitado" id="Habilitado" required>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
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
    let modal = new bootstrap.Modal(document.getElementById('modalEmpleado'));

    function cargarTabla(pagina=1) {
        $("#tablaEmpleados").load("empleados_listar.php?pagina=" + pagina);
    }

    function abrirModal(id=null) {
        if (id) {
            $.get("empleados_get.php", {EmpleadoId: id}, function(data) {
                let emp = JSON.parse(data);
                $("#EmpleadoId").val(emp.EmpleadoId);
                $("#Empleado").val(emp.Empleado);
                $("#Categoria").val(emp.Categoria);
                $("#ValorHora").val(emp.ValorHora);
                $("#Jornal").val(emp.Jornal);
                $("#Habilitado").val(emp.Habilitado);
                modal.show();
            });
        } else {
            $("#formEmpleado")[0].reset();
            $("#EmpleadoId").val('');
            modal.show();
        }
    }

    $("#formEmpleado").on("submit", function(e) {
        e.preventDefault();
        $.post("empleados_abm.php", $(this).serialize(), function(resp) {
            if (resp=="ok") {
                modal.hide();
                alertify.success("✅ Guardado correctamente");
                cargarTabla();
            } else {
                alertify.error("❌ Error: " + resp);
            }
        });
    });

    function eliminarEmpleado(id) {
        alertify.confirm("Eliminar", "¿Desea eliminar este empleado?",
            function(){
                $.post("empleados_abm.php", {accion:"eliminar", EmpleadoId:id}, function(resp){
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
