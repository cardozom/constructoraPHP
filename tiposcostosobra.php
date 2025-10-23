<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION["EmpresaId"]) || !isset($_SESSION["UsuarioId"])) {
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tipos Costo Obra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Alertify2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <!-- jQuery (para simplicidad en AJAX) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tipos de Costo por Obra</h3>
        <div>
            <button id="btnNuevo" class="btn btn-primary">Nuevo</button>
        </div>
    </div>

    <!-- FILTROS / PAGINACIÓN -->
    <div class="row mb-2">
        <div class="col-md-4">
            <input id="q" class="form-control" placeholder="Buscar por Costo..." maxlength="50" autocomplete="off">
        </div>
        <div class="col-md-2">
            <select id="perPage" class="form-control">
                <option value="5">5 por pag</option>
                <option value="10" selected>10 por pag</option>
                <option value="25">25 por pag</option>
            </select>
        </div>
    </div>

    <!-- TABLA -->
    <div id="grilla" class="table-responsive"></div>

    <!-- Modal ABM -->
    <div class="modal fade" id="modalABM" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formABM" class="modal-content" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Tipo Costo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Nota: TipoCosto NO se muestra ni se edita; se toma desde $_SESSION["TipoCosto"] en backend -->
                    <input type="hidden" name="accion" id="accion" value="insertar">
                    <input type="hidden" name="CostoId" id="CostoId" value="">

                    <div class="mb-3">
                        <label for="Costo" class="form-label">Costo</label>
                        <input type="text" class="form-control" name="Costo" id="Costo" maxlength="50" required autocomplete="off">
                    </div>

                    <div class="alert alert-secondary small">
                        <strong>TipoCosto:</strong> se asigna automáticamente desde la sesión.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Bootstrap JS (popper incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function(){
    const modalEl = document.getElementById('modalABM');
    const bsModal = new bootstrap.Modal(modalEl);
    let currentPage = 1;

    function listar(page = 1) {
        const q = $('#q').val();
        const perPage = $('#perPage').val();
        currentPage = page;
        $.ajax({
            url: 'tiposcostosobra_ajax.php',
            method: 'POST',
            data: { accion: 'listar', page: page, q: q, perPage: perPage },
            dataType: 'json'
        }).done(function(res){
            if (!res.success) {
                alertify.error(res.message || 'Error al listar');
                return;
            }
            renderTable(res.data, res.total, res.page, res.perPage);
        }).fail(function(){
            alertify.error('Error en comunicación con el servidor');
        });
    }

    function renderTable(rows, total, page, perPage) {
        let html = '';
        html += '<table class="table table-bordered table-striped">';
        html += '<thead class="table-secondary"><tr><th>ID</th><th>Costo</th><th>TipoCosto</th><th>Acciones</th></tr></thead>';
        html += '<tbody>';
        if (rows.length === 0) {
            html += '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
        } else {
            rows.forEach(r => {
                html += `<tr>
                    <td>${r.CostoId}</td>
                    <td>${escapeHtml(r.Costo)}</td>
                    <td>${escapeHtml(r.TipoCosto)}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${r.CostoId}">Editar</button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${r.CostoId}">Eliminar</button>
                    </td>
                </tr>`;
            });
        }
        html += '</tbody></table>';

        // paginador
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        html += '<nav><ul class="pagination">';
        html += `<li class="page-item ${page<=1?'disabled':''}"><a class="page-link" href="#" data-page="${page-1}">Anterior</a></li>`;
        for (let p = 1; p <= totalPages; p++) {
            html += `<li class="page-item ${p===page?'active':''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
            if (p >= 20) { /* evita crear 1000 botones */ break; }
        }
        html += `<li class="page-item ${page>=totalPages?'disabled':''}"><a class="page-link" href="#" data-page="${page+1}">Siguiente</a></li>`;
        html += '</ul></nav>';

        $('#grilla').html(html);
    }

    // escapar HTML
    function escapeHtml(text) {
        if (!text && text !== 0) return '';
        return $('<div>').text(text).html();
    }

    // eventos
    $('#q').on('keyup', function(e){
        if (e.key === 'Enter') listar(1);
    });
    $('#perPage').on('change', function(){ listar(1); });

    // paginador delegación
    $('#grilla').on('click', '.pagination a', function(e){
        e.preventDefault();
        const p = parseInt($(this).data('page')) || 1;
        if (p < 1) return;
        listar(p);
    });

    // nuevo
    $('#btnNuevo').on('click', function(){
        $('#modalTitle').text('Nuevo Tipo de Costo');
        $('#accion').val('insertar');
        $('#CostoId').val('');
        $('#Costo').val('');
        bsModal.show();
    });

    // editar: obtener registro y abrir modal
    $('#grilla').on('click', '.btn-editar', function(){
        const id = $(this).data('id');
        $.ajax({
            url: 'tiposcostosobra_ajax.php',
            method: 'POST',
            data: { accion: 'obtener', CostoId: id },
            dataType: 'json'
        }).done(function(res){
            if (!res.success) { alertify.error(res.message || 'No encontrado'); return; }
            const d = res.data;
            $('#modalTitle').text('Editar Tipo de Costo');
            $('#accion').val('editar');
            $('#CostoId').val(d.CostoId);
            $('#Costo').val(d.Costo);
            // TipoCosto no se muestra ni edita (toma desde sesión)
            bsModal.show();
        }).fail(function(){ alertify.error('Error al obtener registro'); });
    });

    // eliminar
    $('#grilla').on('click', '.btn-eliminar', function(){
        const id = $(this).data('id');
        alertify.confirm('Eliminar', '¿Eliminar este registro?', function(){
            $.ajax({
                url: 'tiposcostosobra_ajax.php',
                method: 'POST',
                data: { accion: 'eliminar', CostoId: id },
                dataType: 'json'
            }).done(function(res){
                if (res.success) {
                    alertify.success('Eliminado');
                    listar(currentPage);
                } else {
                    alertify.error(res.message || 'Error al eliminar');
                }
            }).fail(function(){ alertify.error('Error servidor'); });
        }, function(){ /* cancel */ });
    });

    // guardar (insertar/editar)
    $('#formABM').on('submit', function(e){
        e.preventDefault();
        const accion = $('#accion').val();
        const Costo = $('#Costo').val().trim();
        if (Costo.length === 0) { alertify.error('Ingrese Costo'); return; }
        if (Costo.length > 50) { alertify.error('Costo muy largo'); return; }

        const data = $(this).serialize();
        $.ajax({
            url: 'tiposcostosobra_ajax.php',
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(res){
            if (res.success) {
                alertify.success(res.message || 'Guardado');
                bsModal.hide();
                listar(1);
            } else {
                alertify.error(res.message || 'Error al guardar');
            }
        }).fail(function(){ alertify.error('Error servidor'); });
    });

    // inicial
    listar(1);
});
</script>

</body>
</html>
