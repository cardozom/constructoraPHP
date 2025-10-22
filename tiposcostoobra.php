<?php
//session_start();  Inicia la sesión
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit();
}

require "conexion.php";

$empresaId = $_SESSION["EmpresaId"];
$tipoCosto = $_SESSION["TipoCosto"];
echo "<h4>Tipo de Costo: $tipoCosto</h4>";
// Manejo de acciones AJAX
if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];

    // Listar con paginación
    if ($accion == "listar") {
        $pagina = isset($_POST["pagina"]) ? (int)$_POST["pagina"] : 1;
        $limite = 5; // cantidad de filas por página
        $inicio = ($pagina - 1) * $limite;

        // Total de registros
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM TiposCostoObra WHERE EmpresaId=? AND TipoCosto=?");
        $stmt->execute([$empresaId, $tipoCosto]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
        $paginas = ceil($total / $limite);

        // Registros de la página
        $stmt = $pdo->prepare("SELECT * FROM TiposCostoObra WHERE EmpresaId=? AND TipoCosto=? ORDER BY CostoId DESC LIMIT $inicio,$limite");
        $stmt->execute([$empresaId, $tipoCosto]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
         echo "Consulta preparada: " . $stmt->queryString . "<br>";
        ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Costo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= $r["CostoId"] ?></td>
                    <td><?= htmlspecialchars($r["Costo"]) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editar(<?= $r['CostoId'] ?>, '<?= htmlspecialchars($r['Costo'], ENT_QUOTES) ?>')">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminar(<?= $r['CostoId'] ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination">
                <?php for($i=1; $i<=$paginas; $i++): ?>
                    <li class="page-item <?= ($i==$pagina)?'active':'' ?>">
                        <a class="page-link" href="javascript:void(0)" onclick="cargarTabla(<?= $i ?>)"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php
        exit();
    }

    // Insertar
    if ($accion == "insertar") {
        if (!empty($_POST["Costo"])) {
            $costo = trim($_POST["Costo"]);
            $stmt = $pdo->prepare("INSERT INTO TiposCostoObra (EmpresaId, Costo, TipoCosto) VALUES (?, ?, ?)");
            echo $stmt->execute([$empresaId, $costo, $tipoCosto]) ? "ok" : "error";
        } else {
            echo "error";
        }
        exit();
    }

    // Eliminar
    if ($accion == "eliminar") {
        if (!empty($_POST["id"])) {
            $stmt = $pdo->prepare("DELETE FROM TiposCostoObra WHERE CostoId=? AND EmpresaId=?");
            echo $stmt->execute([$_POST["id"], $empresaId]) ? "ok" : "error";
        }
        exit();
    }

    // Editar
    if ($accion == "editar") {
        if (!empty($_POST["id"]) && !empty($_POST["Costo"])) {
            $stmt = $pdo->prepare("UPDATE TiposCostoObra SET Costo=? WHERE CostoId=? AND EmpresaId=?");
            echo $stmt->execute([$_POST["Costo"], $_POST["id"], $empresaId]) ? "ok" : "error";
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Costos de Obra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="container py-4">

    <h3 class="mb-4">Gestión de Tipos de Costo de Obra</h3>

    <!-- Formulario -->
    <div class="card p-3 mb-4">
        <form id="formAgregar">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="Costo" id="Costo" class="form-control"
                           maxlength="50" autocomplete="off" placeholder="Nombre del costo" required>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div id="tablaDatos"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
    $(document).ready(function(){
        cargarTabla(1);

        $("#formAgregar").submit(function(e){
            e.preventDefault();
            $.post("tiposcostoobra.php", {accion:"insertar", Costo:$("#Costo").val()}, function(res){
                if(res === "ok"){
                    alertify.success("Costo agregado");
                    $("#formAgregar")[0].reset();
                    cargarTabla(1);
                } else {
                    alertify.error("Error al agregar");
                }
            });
        });
    });

    function cargarTabla(pagina){
        $.post("tiposcostoobra.php", {accion:"listar", pagina:pagina}, function(data){
            $("#tablaDatos").html(data);
        });
    }

    function eliminar(id){
        alertify.confirm("Confirmar", "¿Desea eliminar este costo?",
            function(){
                $.post("tiposcostoobra.php", {accion:"eliminar", id:id}, function(res){
                    if(res === "ok"){
                        alertify.success("Eliminado");
                        cargarTabla(1);
                    } else {
                        alertify.error("Error al eliminar");
                    }
                });
            },
            function(){}
        );
    }

    function editar(id, costo){
        alertify.prompt("Editar Costo", "Modificar el nombre:", costo,
            function(evt, value){
                $.post("tiposcostoobra.php", {accion:"editar", id:id, Costo:value}, function(res){
                    if(res === "ok"){
                        alertify.success("Actualizado");
                        cargarTabla(1);
                    } else {
                        alertify.error("Error al actualizar");
                    }
                });
            },
            function(){}
        ).set('labels', {ok:'Guardar', cancel:'Cancelar'});
    }
    </script>
</body>
</html>
