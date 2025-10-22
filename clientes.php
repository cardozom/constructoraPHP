<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}

// Traer condiciones IVA para el combo
$stmt = $pdo->query("SELECT CondicionIvaId, CondicionIva FROM tblCondicionIva ORDER BY CondicionIva");
$condicionesIva = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container py-4">

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">Clientes</h4>
            <button class="btn btn-light btn-sm" onclick="abrirModal()">➕ Nuevo</button>
        </div>
        <div class="card-body" id="tablaClientes">
            <!-- Aquí carga la tabla con AJAX -->
        </div>
    </div>

    <!-- Modal ABM -->
    <div class="modal fade" id="modalCliente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formCliente" class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ClienteId" id="ClienteId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control" name="Cliente" id="Cliente" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Condición IVA</label>
                            <select class="form-control" name="CondicionIvaId" id="CondicionIvaId" required>
                                <option value="">-- Seleccione --</option>
                                <?php foreach($condicionesIva as $c): ?>
                                    <option value="<?= $c['CondicionIvaId'] ?>"><?= htmlspecialchars($c['CondicionIva']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CUIT</label>
                            <input type="text" class="form-control" name="CUIT" id="CUIT" maxlength="13" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="Direccion" id="Direccion" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Localidad</label>
                            <input type="text" class="form-control" name="Localidad" id="Localidad" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" class="form-control" name="Provincia" id="Provincia" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="Telefono1" id="Telefono1" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" maxlength="50" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Habilitado</label>
                            <select class="form-control" name="Habilitado" id="Habilitado" required>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Saldo</label>
                            <input type="number" step="0.01" class="form-control" name="Saldo" id="Saldo" autocomplete="off" required>
                        </div>
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
    let modal = new bootstrap.Modal(document.getElementById('modalCliente'));

    function cargarTabla(pagina=1) {
        $("#tablaClientes").load("clientes_listar.php?pagina=" + pagina);
    }

    function abrirModal(id=null) {
        if (id) {
            $.get("clientes_get.php", {ClienteId: id}, function(data) {
                let cli = JSON.parse(data);
                $("#ClienteId").val(cli.ClienteId);
                $("#Cliente").val(cli.Cliente);
                $("#CondicionIvaId").val(cli.CondicionIvaId);
                $("#CUIT").val(cli.CUIT);
                $("#Direccion").val(cli.Direccion);
                $("#Localidad").val(cli.Localidad);
                $("#Provincia").val(cli.Provincia);
                $("#Telefono1").val(cli.Telefono1);
                $("#email").val(cli.email);
                $("#Habilitado").val(cli.Habilitado);
                $("#Saldo").val(cli.Saldo);
                modal.show();
            });
        } else {
            $("#formCliente")[0].reset();
            $("#ClienteId").val('');
            modal.show();
        }
    }

    $("#formCliente").on("submit", function(e) {
        e.preventDefault();
        $.post("clientes_abm.php", $(this).serialize(), function(resp) {
            if (resp=="ok") {
                modal.hide();
                alertify.success("✅ Guardado correctamente");
                cargarTabla();
            } else {
                alertify.error("❌ Error: " + resp);
            }
        });
    });

    function eliminarCliente(id) {
        alertify.confirm("Eliminar", "¿Desea eliminar este cliente?",
            function(){
                $.post("clientes_abm.php", {accion:"eliminar", ClienteId:id}, function(resp){
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
