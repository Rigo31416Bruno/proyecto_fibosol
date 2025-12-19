<?php
include '../backend/validarSesion.php';
require('../db/conexion.php');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$sexo = isset($_GET['sexo']) ? (int)$_GET['sexo'] : 0;
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

$productos = [];
$categorias_principales = [];
$categorias_filtradas = [];

// Hombre y Mujer
if (isset($conn) && $conn) {
    $sql_principales = "SELECT id_categoria, nombre FROM categorias WHERE ParentID IS NULL ORDER BY id_categoria";
    $res = mysqli_query($conn, $sql_principales);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $categorias_principales[] = $row;
        }
        mysqli_free_result($res);
    }

    // Si se selecciona un sexo obtener sus subcategorías
    if ($sexo > 0) {
        $sql_sub = "SELECT id_categoria, nombre FROM categorias WHERE ParentID = " . (int)$sexo . " ORDER BY nombre";
        $res = mysqli_query($conn, $sql_sub);
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $categorias_filtradas[] = $row;
            }
            mysqli_free_result($res);
        }
    }

    $sql = "SELECT id_producto, nombre, precio, imagen FROM productos WHERE 1=1";
    
    if (!empty($query)) {
        $query_escaped = mysqli_real_escape_string($conn, $query);
        $sql .= " AND nombre LIKE '%{$query_escaped}%'";
    }
    
    if ($categoria > 0) {
        $sql .= " AND categoria = " . (int)$categoria;
    } elseif ($sexo > 0) {
        $sql .= " AND categoria IN (SELECT id_categoria FROM categorias WHERE ParentID = " . (int)$sexo . ")";
    }
    
    $sql .= " ORDER BY nombre LIMIT 50";
    
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $productos[] = $row;
        }
        mysqli_free_result($res);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Busqueda - Shopware</title>
    <link rel="stylesheet" href="../styles/inicio.css">
</head>
<body>
    
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">Shopware©</div>
            <button class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="inicio.php#camisas">Camisas</a></li>
                <li><a href="inicio.php#pantalones">Pantalones</a></li>
                <li><a href="inicio.php#tenis">Tenis</a></li>
                <li><a href="inicio.php#contacto">Contacto</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="login.html" class="btn-login">Inicia Sesión</a>
                <a href="registro.html" class="btn-register">Regístrate</a>
            </div>
            <button id="cartToggle" class="cart-icon" aria-label="Abrir carrito">
                🛒 <span class="cart-count" id="cartCount">0</span>
            </button>
        </div>
    </nav>

    
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <span>Hola <?php echo $_SESSION['nombre']; ?></span>
            <button class="close-cart" id="closeCart">&times;</button>
        </div>
        <div class="cart-items" id="cartItems">
                <div class="empty-cart">
                    <p>Ve a tu carrito para ver los productos añadidos.</p>
                </div>
        </div>
        <div class="sidebar-actions">
            <a href="carrito.php" class="btn-primary">Ir al Carrito</a>
            <a href="direccion.php" class="sidebar-btn">Gestionar Direcciones</a>
            <button class="sidebar-btn sidebar-logout" onclick="openLogoutModal()">Cerrar Sesión</button>
        </div>
    </div>

    <section class="search-results">
        <div class="results-container">
            <?php if (!empty($productos)): ?>
                <div class="results-header">
                    <h2>Se encontraron <?php echo count($productos); ?> producto(s)</h2>
                </div>
                <div class="products-grid">
                    <?php foreach ($productos as $producto): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php $img = !empty($producto['imagen']) ? $producto['imagen'] : '/placeholder.svg?height=280&width=280'; ?>
                                <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>">
                            </div>
                            <h3><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?></h3>
                            <p class="price">$<?php echo number_format((float)$producto['precio'], 2); ?></p>
                            <a href="agregarCarrito.php?id=<?php echo (int)$producto['id_producto']; ?>" class="btn-add-cart" role="button">Agregar al Carrito</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <h2>No se encontraron productos</h2>
                    <p><?php echo !empty($query) ? "Intenta con otros términos de búsqueda o usa los filtros." : "Selecciona un filtro para ver productos."; ?></p>
                    <a href="inicio.php" class="btn-primary">Volver al Inicio</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre Nosotros</h4>
                <p>Shopware es tu destino para ropa casual de calidad a precios accesibles.</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="buscar.php">Búsqueda Avanzada</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>Email: info@Shopware.com</p>
                <p>Teléfono: +1 (555) 123-4567</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Shopware. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../js/sidebar_funcionalidad.js"></script>
    <script src="../js/modalCerrarSesion.js"></script>

<div id="logoutModal" class="logout-modal">
    <div class="logout-modal-content">
        <div class="logout-modal-header">
            <h2>¿Cerrar sesión?</h2>
        </div>
        <div class="logout-modal-body">
            <p>¿Estás seguro de que deseas cerrar sesión?</p>
        </div>
        <div class="logout-modal-footer">
            <button class="logout-btn-cancel" onclick="closeLogoutModal()">Cancelar</button>
            <form method="POST" action="../backend/cerrarSesion.php" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn-confirm">Confirmar Salida</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
