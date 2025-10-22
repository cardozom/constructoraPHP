<?php
session_start();
if (isset($_SESSION["UsuarioId"])) {
    header("Location: inicio.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center">Iniciar Sesión</h3>
        <form id="loginForm" method="POST" action="validarusuario.php">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" autocomplete="off" class="form-control" name="usuario" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Clave</label>
                <input type="password" autocomplete="off" class="form-control" name="clave" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

<?php
if (isset($_GET["error"])) {
    echo "<script>alertify.error('Usuario o clave incorrectos');</script>";
}
?>
</body>
</html>
