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

$query = "SELECT f.*, u.Nombre as usuario_nombre, u.Correo as usuario_correo, p.total as pedido_total
          FROM facturas f
          LEFT JOIN usuarios u ON f.id_usuario = u.ID_Usuario
          LEFT JOIN pedidos p ON f.id_pedido = p.id_pedido
          WHERE 1=1";
$params = [];
$types = '';

if ($buscar !== '') {
    if (ctype_digit($buscar)) {
        $query .= " AND f.id_factura = ?";
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
    $query .= " AND f.fecha_creacion >= ?";
    $params[] = $fecha_inicio . ' 00:00:00';
    $types .= 's';
}
if ($fecha_fin !== '') {
    $query .= " AND f.fecha_creacion <= ?";
    $params[] = $fecha_fin . ' 23:59:59';
    $types .= 's';
}

$query .= " ORDER BY f.id_factura DESC";

$facturas = [];
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
        while ($row = mysqli_fetch_assoc($res)) $facturas[] = $row;
    }
    mysqli_stmt_close($stmt);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Facturas - Admin</title>
    <link rel="stylesheet" href="../styles/administrador.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="content-header">
                <div>
                    <h2>Facturas</h2>
                    <p class="header-subtitle">Listado de facturas y enlaces a PDFs</p>
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
                                <th>Pedido</th>
                                <th>Fecha</th>
                                <th>Ruta PDF</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($facturas)): ?>
                                <tr><td colspan="6" class="empty-state">No se encontraron facturas</td></tr>
                            <?php else: ?>
                                <?php foreach ($facturas as $f): ?>
                                    <tr>
                                        <td><span class="badge">#<?php echo $f['id_factura']; ?></span></td>
                                        <td><?php echo htmlspecialchars($f['usuario_nombre'] ?? ''); ?></td>
                                        <td><?php echo $f['id_pedido'] ? ('#' . $f['id_pedido'] . ' ($' . number_format($f['pedido_total'] ?? 0,2) . ')') : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($f['fecha_creacion']); ?></td>
                                        <td><?php echo htmlspecialchars($f['ruta_pdf']); ?></td>
                                        <td>
                                            <?php if (!empty($f['ruta_pdf'])): ?>
                                                <a class="btn" href="<?php echo htmlspecialchars($f['ruta_pdf']); ?>" target="_blank">Abrir PDF</a>
                                                <a class="btn btn-outline" href="<?php echo htmlspecialchars($f['ruta_pdf']); ?>" download>Descargar</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
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
</body>
</html>
