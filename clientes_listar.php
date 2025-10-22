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

$total = $pdo->prepare("SELECT COUNT(*) FROM Clientes WHERE EmpresaId=?");
$total->execute([$empresaId]);
$totalReg = $total->fetchColumn();
$totalPaginas = ceil($totalReg / $porPagina);

$sql = "SELECT c.*, i.CondicionIva 
        FROM Clientes c
        INNER JOIN tblCondicionIva i ON c.CondicionIvaId = i.CondicionIvaId
        WHERE c.EmpresaId=?
        ORDER BY ClienteId DESC LIMIT $inicio,$porPagina";
$stmt = $pdo->prepare($sql);
$stmt->execute([$empresaId]);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Cliente</th>
            <th>CUIT</th>
            <th>Condición IVA</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Habilitado</th>
            <th>Saldo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['Cliente']) ?></td>
            <td><?= htmlspecialchars($c['CUIT']) ?></td>
            <td><?= htmlspecialchars($c['CondicionIva']) ?></td>
            <td><?= htmlspecialchars($c['Telefono1']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= $c['Habilitado'] ?></td>
            <td><?= $c['Saldo'] ?></td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="abrirModal(<?= $c['ClienteId'] ?>)">✏️</button>
                <button class="btn btn-sm btn-danger" onclick="eliminarCliente(<?= $c['ClienteId'] ?>)">🗑️</button>
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
