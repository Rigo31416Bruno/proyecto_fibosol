<?php
session_start();
require_once '../db/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header('Location: ../frontend/inicio_de_sesion.html');
    exit();
}

// Obtener estadísticas
$stats = [];

// Total de productos
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM productos");
$row = mysqli_fetch_assoc($result);
$stats['productos'] = $row['total'] ?? 0;

// Total de usuarios
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
$row = mysqli_fetch_assoc($result);
$stats['usuarios'] = $row['total'] ?? 0;

// Total de pedidos
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pedidos");
$row = mysqli_fetch_assoc($result);
$stats['pedidos'] = $row['total'] ?? 0;

// Ventas totales
$result = mysqli_query($conn, "SELECT COALESCE(SUM(total), 0) as total FROM pedidos");
$row = mysqli_fetch_assoc($result);
$stats['ventas'] = $row['total'] ?? 0;

// Productos con bajo stock
$result = mysqli_query($conn, "SELECT COUNT(DISTINCT id_producto) as total FROM producto_tallas WHERE stock <= 5");
$row = mysqli_fetch_assoc($result);
$stats['bajo_stock'] = $row['total'] ?? 0;

// Pedidos recientes
$result = mysqli_query($conn, "SELECT p.id_pedido, p.fecha_pedido, p.total, u.Nombre 
    FROM pedidos p 
    JOIN usuarios u ON p.id_usuario = u.ID_Usuario 
    ORDER BY p.fecha_pedido DESC 
    LIMIT 5");
$pedidos_recientes = [];
if ($result) {
    while ($r = mysqli_fetch_assoc($result)) {
        $pedidos_recientes[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Tienda</title>
    <link rel="stylesheet" href="../styles/administrador.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Admin Panel</h1>
                <p class="admin-name"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="productos.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                    Productos
                </a>
                <a href="usuarios.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Usuarios
                </a>
                <a href="pedidos.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Pedidos
                </a>
                <a href="categorias.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                    Categorías
                </a>
                <a href="facturas.php" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Facturas
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="../backend/cerrarSesion.php" class="logout-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h2>Dashboard</h2>
                <p class="header-subtitle">Bienvenido al panel de administración</p>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Productos</p>
                        <p class="stat-value"><?php echo $stats['productos']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Usuarios</p>
                        <p class="stat-value"><?php echo $stats['usuarios']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Pedidos</p>
                        <p class="stat-value"><?php echo $stats['pedidos']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-orange">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Ventas Totales</p>
                        <p class="stat-value">$<?php echo number_format($stats['ventas'], 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Alert for low stock -->
            <?php if ($stats['bajo_stock'] > 0): ?>
            <div class="alert-banner">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Hay <strong><?php echo $stats['bajo_stock']; ?></strong> producto(s) con bajo stock. <a href="productos.php">Ver productos</a></span>
            </div>
            <?php endif; ?>

            <!-- Recent Orders -->
            <div class="content-section">
                <div class="section-header">
                    <h3>Pedidos Recientes</h3>
                    <a href="pedidos.php" class="view-all-btn">Ver todos</a>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pedidos_recientes)): ?>
                            <tr>
                                <td colspan="5" class="empty-state">No hay pedidos registrados</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($pedidos_recientes as $pedido): ?>
                                <tr>
                                    <td><span class="badge">#<?php echo $pedido['id_pedido']; ?></span></td>
                                    <td><?php echo htmlspecialchars($pedido['Nombre']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                                    <td class="price">$<?php echo number_format($pedido['total'], 2); ?></td>
                                    <td>
                                        <a href="pedidos.php?ver=<?php echo $pedido['id_pedido']; ?>" class="action-btn action-btn-view">
                                            Ver
                                        </a>
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

    <script>
        function eliminarProducto(id) {
  if (confirm("¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.")) {
    fetch("admin_backend/eliminarProducto.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id_producto: id }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.reload()
        } else {
          alert("Error: " + data.message)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        alert("Error al eliminar el producto")
      })
  }
}

function abrirModalNuevoProducto() {
  window.location.href = "editar_producto.php"
}

    </script>
</body>
</html>
