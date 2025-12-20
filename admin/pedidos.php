<?php
session_start();
require_once '../db/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header('Location: ../frontend/inicio_de_sesion.html');
    exit();
}

$buscar = $_GET['buscar'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$query = "SELECT p.*, u.Nombre as usuario_nombre, u.Correo as usuario_correo FROM pedidos p LEFT JOIN usuarios u ON p.id_usuario = u.ID_Usuario WHERE 1=1";
$params = [];
$types = '';

if ($buscar !== '') {
    if (ctype_digit($buscar)) {
        $query .= " AND p.id_pedido = ?";
        $params[] = (int)$buscar;
        $types .= 'i';
    } else {
        $query .= " AND (u.Nombre LIKE ? OR u.Correo LIKE ?)";
        $params[] = "%$buscar%";
        $params[] = "%$buscar%";
        $types .= 'ss';
    }
}

if ($fecha_inicio !== '') {
    $query .= " AND p.fecha_pedido >= ?";
    $params[] = $fecha_inicio . ' 00:00:00';
    $types .= 's';
}
if ($fecha_fin !== '') {
    $query .= " AND p.fecha_pedido <= ?";
    $params[] = $fecha_fin . ' 23:59:59';
    $types .= 's';
}

$query .= " ORDER BY p.id_pedido DESC";

$pedidos = [];
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    if (!empty($params)) {
        $bind_names = [];
        $bind_names[] = $stmt;
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = & $params[$i];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_names);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) $pedidos[] = $row;
    }
    mysqli_stmt_close($stmt);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pedidos - Admin</title>
    <link rel="stylesheet" href="../styles/administrador.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="content-header">
                <div>
                    <h2>Pedidos</h2>
                    <p class="header-subtitle">Buscar y ver detalles de pedidos</p>
                </div>
            </header>

            <div class="content-section">
                <form method="GET" action="">
                    <div class="filters-row">
                        <div class="filter-group">
                            <input type="text" name="buscar" class="form-input" placeholder="Buscar por ID, nombre o correo" value="<?php echo htmlspecialchars($buscar); ?>">
                        </div>
                        <div class="filter-group">
                            <input type="date" name="fecha_inicio" class="form-input" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                        </div>
                        <div class="filter-group">
                            <input type="date" name="fecha_fin" class="form-input" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            <div class="content-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos)): ?>
                                <tr><td colspan="6" class="empty-state">No se encontraron pedidos</td></tr>
                            <?php else: ?>
                                <?php foreach ($pedidos as $p): ?>
                                <tr>
                                    <td><span class="badge">#<?php echo $p['id_pedido']; ?></span></td>
                                    <td><?php echo htmlspecialchars($p['usuario_nombre'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($p['usuario_correo'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($p['fecha_pedido']); ?></td>
                                    <td class="price">$<?php echo number_format($p['total'],2); ?></td>
                                    <td>
                                        <button class="action-btn action-btn-edit action-btn-view" data-id="<?php echo $p['id_pedido']; ?>">Ver detalles</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="orderModal" class="modal-overlay">
        <div class="modal-container" style="max-width:720px;">
            <h3 id="orderModalTitle">Detalle de Pedido</h3>
            <div id="orderInfo" style="margin-top:12px;">
                <!-- cargado por AJAX -->
            </div>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                <button id="orderCloseBtn" class="btn-modal btn-outline">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('.table-container')?.addEventListener('click', function(e){
            const btn = e.target.closest('.action-btn-view');
            if (!btn) return;
            const id = btn.dataset.id;
            $('#orderModal').addClass('show');
            $('#orderInfo').html('<p>Cargando...</p>');
            $.getJSON('admin_backend/obtenerPedido.php', { id_pedido: id }, function(resp){
                if (resp && resp.success) {
                    let html = '<p><strong>Pedido #'+resp.order.id_pedido+'</strong> — Usuario: '+(resp.order.usuario_nombre||'')+' ('+(resp.order.usuario_correo||'')+')';
                    html += '<br>Fecha: '+resp.order.fecha_pedido+' — Total: $'+parseFloat(resp.order.total).toFixed(2)+'</p>';
                    html += '<table class="data-table" style="margin-top:8px;"><thead><tr><th>Producto</th><th>Talla</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr></thead><tbody>';
                    (resp.items||[]).forEach(function(it){
                        const subtotal = (parseFloat(it.cantidad) * parseFloat(it.precio_unitario)).toFixed(2);
                        html += '<tr><td>'+ (it.nombre || '') +'</td><td>'+ (it.talla_nombre || '') +'</td><td>'+it.cantidad+'</td><td>$'+parseFloat(it.precio_unitario).toFixed(2)+'</td><td>$'+subtotal+'</td></tr>';
                    });
                    html += '</tbody></table>';
                    $('#orderInfo').html(html);
                } else {
                    $('#orderInfo').html('<div style="color:#f66;">'+(resp?.message||'No se pudo cargar pedido')+'</div>');
                }
            }).fail(function(){ $('#orderInfo').html('<div style="color:#f66;">Error de conexión</div>'); });
        });

        document.getElementById('orderCloseBtn').addEventListener('click', function(){
            document.getElementById('orderModal').classList.remove('show');
        });
        window.addEventListener('click', function(e){ if (e.target === document.getElementById('orderModal')) document.getElementById('orderModal').classList.remove('show'); });
    </script>
</body>
</html>
