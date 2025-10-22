<?php
session_start();
require_once "conexion.php";

// Verificar sesión
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}

$empresaId = $_SESSION["EmpresaId"];

// Obtener datos de la empresa
$stmt = $pdo->prepare("SELECT * FROM EmpresaConstructora WHERE EmpresaId = ?");
$stmt->execute([$empresaId]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    echo "<div class='alert alert-danger'>Empresa no encontrada</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container py-4">

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Datos de la Empresa</h4>
        </div>
        <div class="card-body">

            <form id="formEmpresa">

                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Empresa</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['Empresa']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">CUIT</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['Cuit']) ?>" readonly>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['Direccion']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Localidad</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($empresa['Localidad']) ?>" readonly>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Saldo</label>
                            <input type="number" step="0.01" class="form-control" name="Saldo" value="<?= htmlspecialchars($empresa['Saldo']) ?>">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Fecha Inicio Ejercicio</label>
                            <input type="datetime-local" class="form-control" name="FechaInicioEj" 
                                value="<?= date('Y-m-d\TH:i', strtotime($empresa['FechaInicioEj'])) ?>">
                        </div>
                    </div>

                </div>








                <input type="hidden" name="EmpresaId" value="<?= $empresa['EmpresaId'] ?>">

                <div class="text-end">
                    <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
    $("#formEmpresa").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "empresa_update.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == "ok") {
                    alertify.success("✅ Datos actualizados correctamente");
                } else {
                    alertify.error("❌ Error al actualizar: " + resp);
                }
            },
            error: function() {
                alertify.error("⚠️ Error en la comunicación con el servidor");
            }
        });
    });
    </script>


</body>
</html>
