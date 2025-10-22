<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    exit("Sesión no válida");
}

$empresaId = $_SESSION["EmpresaId"];
$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $porPagina;

$total = $pdo->prepare("SELECT COUNT(*) FROM Empleados WHERE EmpresaId=?");
$total->execute([$empresaId]);
$totalReg = $total->fetchColumn();
$totalPaginas = ceil($totalReg / $porPagina);

$stmt = $pdo->prepare("SELECT * FROM Empleados WHERE EmpresaId=? ORDER BY EmpleadoId DESC LIMIT $inicio,$porPagina");
$stmt->execute([$empresaId]);
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Empleado</th>
            <th>Categoría</th>
            <th>Valor Hora</th>
            <th>Jornal</th>
            <th>Habilitado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($empleados as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['Empleado']) ?></td>
            <td><?= htmlspecialchars($e['Categoria']) ?></td>
            <td><?= $e['ValorHora'] ?></td>
            <td><?= $e['Jornal'] ?></td>
            <td><?= $e['Habilitado'] ?></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="abrirModal(<?= $e['EmpleadoId'] ?>)">✏️</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarEmpleado(<?= $e['EmpleadoId'] ?>)">🗑️</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav>
    <ul class="pagination">
        <?php for($i=1;$i<=$totalPaginas;$i++): ?>
            <li class="page-item <?= ($i==$pagina)?'active':'' ?>">
                <a class="page-link" href="javascript:cargarTabla(<?= $i ?>)"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
