<?php
session_start();
if (!isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit;
}
require "conexion.php";

// Traer menú y submenús
$sqlMenu = "SELECT m.MenuId, m.NombreMenu, s.SubMenuId, s.NombreSubMenu
            FROM menus m
            LEFT JOIN submenus s ON m.MenuId = s.MenuId
            ORDER BY m.MenuId, s.SubMenuId";
$stmt = $pdo->query($sqlMenu);
$menus = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Cabecera -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
                <i class="fa fa-bars"></i>
            </button>
            <span class="navbar-text text-white ms-3">
                Bienvenido: <?php echo $_SESSION["Usuario"]; ?> | Empresa: <?php echo $_SESSION["Empresa"]; ?>
            </span>
        </div>
    </nav>

    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menú Principal</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="accordion" id="accordionMenu">
                <?php foreach ($menus as $menuId => $items): ?>
                    <div class="accordion-item bg-dark border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $menuId; ?>">
                                <i class="fa fa-folder me-2"></i> <?php echo $items[0]["NombreMenu"]; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $menuId; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionMenu">
                            <div class="accordion-body p-0">
                                <ul class="nav flex-column">
                                    <?php foreach ($items as $sub): ?>
                                        <?php if (!empty($sub["SubMenuId"])): ?>
                                            <li class="nav-item">
                                                <a href="contenido.php?id=<?php echo $sub['SubMenuId']; ?>" class="nav-link text-white px-3 py-2 load-content" data-bs-dismiss="offcanvas">
                                                    <i class="fa fa-angle-right me-2"></i> <?php echo $sub["NombreSubMenu"]; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container mt-4">
        <div id="mainContent">
            <h3>Panel de inicio</h3>
            <p>Seleccione una opción del menú para cargar contenido.</p>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Cargar contenido dinámicamente
        $(document).on("click", ".load-content", function(e) {
            e.preventDefault();
            var url = $(this).attr("href");
            $("#mainContent").load(url);
        });
    </script>
</body>
</html>
