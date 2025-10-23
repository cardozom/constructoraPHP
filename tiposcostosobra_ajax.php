<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    echo json_encode(['success'=>false, 'message'=>'No hay sesión activa']);
    exit;
}

require_once "conexion.php"; // debe dejar $pdo
$empresaId = (int) $_SESSION["EmpresaId"];
$tipoCostoSession = isset($_SESSION["TipoCosto"]) ? $_SESSION["TipoCosto"] : null;
if ($tipoCostoSession === null) {
    echo json_encode(['success'=>false, 'message'=>'Falta TipoCosto en sesión']);
    exit;
}

// Helper: lectura acción
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

try {
    if ($accion === 'listar') {
        $page = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
        $perPage = isset($_POST['perPage']) ? max(1, (int)$_POST['perPage']) : 10;
        $q = isset($_POST['q']) ? trim($_POST['q']) : '';
        $offset = ($page - 1) * $perPage;

        $params = [$empresaId];
        $where = " WHERE EmpresaId = ? ";
        if ($q !== '') {
            $where .= " AND Costo LIKE ? ";
            $params[] = "%$q%";
        }

        // total
        $sqlTotal = "SELECT COUNT(*) FROM TiposCostoObra $where";
        $stmtTotal = $pdo->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $total = (int) $stmtTotal->fetchColumn();

        // datos
        $sql = "SELECT CostoId, Costo, TipoCosto FROM TiposCostoObra $where ORDER BY CostoId DESC LIMIT ? OFFSET ?";
        $stmt = $pdo->prepare($sql);
        // bind params (rebuild because of LIMIT/OFFSET)
        $execParams = $params;
        $execParams[] = $perPage;
        $execParams[] = $offset;
        // PDO with MySQL accepts LIMIT as integers; ensure proper param types
        $stmt->execute($execParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success'=>true, 'data'=>$rows, 'total'=>$total, 'page'=>$page, 'perPage'=>$perPage]);
        exit;
    }

    if ($accion === 'insertar') {
        // campos desde POST y session
        $Costo = isset($_POST['Costo']) ? trim($_POST['Costo']) : '';
        // validaciones servidor
        if ($Costo === '' || mb_strlen($Costo) > 50) {
            echo json_encode(['success'=>false, 'message'=>'Costo inválido']);
            exit;
        }
        $TipoCosto = $tipoCostoSession;
        // Insert
        $stmt = $pdo->prepare("INSERT INTO TiposCostoObra (EmpresaId, Costo, TipoCosto) VALUES (?, ?, ?)");
        $stmt->execute([$empresaId, $Costo, $TipoCosto]);
        echo json_encode(['success'=>true, 'message'=>'Insertado']);
        exit;
    }

    if ($accion === 'obtener') {
        $CostoId = isset($_POST['CostoId']) ? (int)$_POST['CostoId'] : 0;
        if ($CostoId <= 0) { echo json_encode(['success'=>false, 'message'=>'ID inválido']); exit; }
        $stmt = $pdo->prepare("SELECT CostoId, Costo, TipoCosto FROM TiposCostoObra WHERE CostoId=? AND EmpresaId=?");
        $stmt->execute([$CostoId, $empresaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) { echo json_encode(['success'=>false, 'message'=>'No encontrado']); exit; }
        echo json_encode(['success'=>true, 'data'=>$row]);
        exit;
    }

    if ($accion === 'editar') {
        $CostoId = isset($_POST['CostoId']) ? (int)$_POST['CostoId'] : 0;
        $Costo = isset($_POST['Costo']) ? trim($_POST['Costo']) : '';
        if ($CostoId <= 0) { echo json_encode(['success'=>false, 'message'=>'ID inválido']); exit; }
        if ($Costo === '' || mb_strlen($Costo) > 50) {
            echo json_encode(['success'=>false, 'message'=>'Costo inválido']); exit;
        }
        $TipoCosto = $tipoCostoSession;

        $stmt = $pdo->prepare("UPDATE TiposCostoObra SET Costo = ?, TipoCosto = ? WHERE CostoId = ? AND EmpresaId = ?");
        $stmt->execute([$Costo, $TipoCosto, $CostoId, $empresaId]);
        echo json_encode(['success'=>true, 'message'=>'Actualizado']);
        exit;
    }

    if ($accion === 'eliminar') {
        $CostoId = isset($_POST['CostoId']) ? (int)$_POST['CostoId'] : 0;
        if ($CostoId <= 0) { echo json_encode(['success'=>false, 'message'=>'ID inválido']); exit; }
        $stmt = $pdo->prepare("DELETE FROM TiposCostoObra WHERE CostoId = ? AND EmpresaId = ?");
        $stmt->execute([$CostoId, $empresaId]);
        echo json_encode(['success'=>true, 'message'=>'Eliminado']);
        exit;
    }

    // acción no reconocida
    echo json_encode(['success'=>false, 'message'=>'Acción no válida']);

} catch (PDOException $ex) {
    echo json_encode(['success'=>false, 'message'=>'Error de base de datos: '.$ex->getMessage()]);
    exit;
}
