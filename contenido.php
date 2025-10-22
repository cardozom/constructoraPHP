<?php
session_start();
$id = $_GET["id"] ?? 0;
?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Contenido del Submenú <?php echo $id; ?></h5>
       
<?php 
    switch ($id) {
    case 0:
        echo "i igual 0";
        break;
    case 1:
        echo "i igual 1";
        break;
    case 36:
        include 'empresa.php';
        break;
    case 37:
        include 'empleados.php';
        break;
    case 38:
        include 'clientes.php';
        break;  
    case 39:
        include 'proveedores.php';
        break;         
    case 40:
        include 'tiposcostoempresa.php';
        break;  
    case 41:
        $_SESSION["TipoCosto"] = "CM";
        include 'tiposcostoobra.php';
        break;
    case 42:
        $_SESSION["TipoCosto"] = "CD";
        include 'tiposcostoobra.php';
        break;
    case 43:
        $_SESSION["TipoCosto"] = "CI";
        include 'tiposcostoobra.php';
        break;               

}

?>

    </div>
</div>