<?php
session_start();
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: ../index.php");
    exit();
}
$empresaId = $_SESSION["EmpresaId"];
require_once("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body>
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-success text-white d-flex justify-content-between">
            <h5 class="mb-0">Proveedores</h5>
            <button class="btn btn-light btn-sm" onclick="abrirModal()">➕ Nuevo Proveedor</button>
        </div>
        <div class="card-body">
            <div id="tablaProveedores"></div>
        </div>
    </div>
</div>

<!-- Modal ABM -->
<div class="modal fade" id="modalProveedor" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formProveedor">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="tituloModal">Nuevo Proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <input type="hidden" name="proveedorId" id="proveedorId">

          <div class="col-md-6">
            <label class="form-label">Proveedor</label>
            <input type="text" name="Proveedor" id="Proveedor" maxlength="50" autocomplete="off" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">CUIT</label>
            <input type="text" name="CUIT" id="CUIT" maxlength="13" autocomplete="off" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" name="Direccion" id="Direccion" maxlength="50" autocomplete="off" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Localidad</label>
            <input type="text" name="Localidad" id="Localidad" maxlength="50" autocomplete="off" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Provincia</label>
            <input type="text" name="Provincia" id="Provincia" maxlength="50" autocomplete="off" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="Telefono1" id="Telefono1" maxlength="50" autocomplete="off" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" maxlength="50" autocomplete="off" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Condición IVA</label>
            <select name="CondicionIvaId" id="CondicionIvaId" class="form-select" required>
              <option value="">Seleccione...</option>
              <?php
              $stmt = $pdo->query("SELECT * FROM tblCondicionIva ORDER BY CondicionIva");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  echo "<option value='{$row['CondicionIvaId']}'>{$row['CondicionIva']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Habilitado</label>
            <select name="Habilitado" id="Habilitado" class="form-select">
              <option value="SI">SI</option>
              <option value="NO">NO</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Saldo</label>
            <input type="number" step="0.01" name="Saldo" id="Saldo" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
function cargarProveedores(pagina=1){
    $.get("proveedores_ajax.php",{accion:"listar",pagina:pagina},function(data){
        $("#tablaProveedores").html(data);
    });
}
function abrirModal(proveedor=null){
    if(proveedor){
        $("#tituloModal").text("Editar Proveedor");
        for(const key in proveedor){
            $("#"+key).val(proveedor[key]);
        }
    } else {
        $("#tituloModal").text("Nuevo Proveedor");
        $("#formProveedor")[0].reset();
        $("#proveedorId").val("");
    }
    new bootstrap.Modal(document.getElementById('modalProveedor')).show();
}
$("#formProveedor").submit(function(e){
    e.preventDefault();
    $.post("proveedores_ajax.php",$(this).serialize()+"&accion=guardar",function(resp){
        alertify.success(resp);
        cargarProveedores();
        bootstrap.Modal.getInstance(document.getElementById('modalProveedor')).hide();
    });
});
function eliminarProveedor(id){
    alertify.confirm("¿Eliminar proveedor?","Esta acción no se puede deshacer",function(){
        $.post("proveedores_ajax.php",{accion:"eliminar",proveedorId:id},function(resp){
            alertify.success(resp);
            cargarProveedores();
        });
    },function(){});
}
$(document).ready(function(){ cargarProveedores(); });
</script>
</body>
</html>
