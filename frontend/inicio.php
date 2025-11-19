<?php
include '../backend/validarSesion.php';
require('../db/conexion.php');

if(!validarSesion())
{
    header('Location: inicio_de_sesion.html');
    exit();
}

$categorias_padres = [];
if (isset($conn) && $conn) {
    $sql = "SELECT id_categoria, nombre FROM categorias WHERE ParentID IS NULL ORDER BY nombre";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $categorias_padres[] = $row;
        }
        mysqli_free_result($res);
    }
}

$categorias_sub = [];
if (isset($conn) && $conn) {
    $sql = "SELECT id_categoria, nombre FROM categorias WHERE ParentID IS NOT NULL ORDER BY nombre";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $categorias_sub[] = $row;
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
    <title>Shopware - Tu Tienda de Ropa</title>
    <link rel="stylesheet" href="../styles/inicio.css">
</head>
<body>
    
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">Shopware&copy;</div>
            <button class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#camisas">Camisas</a></li>
                <li><a href="#pantalones">Pantalones</a></li>
                <li><a href="#tenis">Tenis</a></li>
                <li><a href="#contacto">Contacto</a></li>
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

    <!-- Enhanced search banner with separate gender and category filters -->
    <section class="search-banners">
        <div class="search-container">
            <!-- SEARCH 1: Text Search -->
            <div class="search-banner search-banner-text">
                <div class="search-content">
                    <h3 class="search-title">Buscar por Nombre</h3>
                    <form class="search-form" action="buscar.php" method="get" role="search">
                        <div class="search-input-wrapper">
                            <input 
                                type="search" 
                                name="q" 
                                class="search-input" 
                                placeholder="Busca camisas, pantalones, tenis..." 
                                aria-label="Buscar productos por nombre"
                            >
                            <button type="submit" class="search-button">
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SEARCH 2: Gender Search -->
            <div class="search-banner search-banner-gender">
                <div class="search-content">
                    <h3 class="search-title">Filtrar por Sexo</h3>
                    <form class="search-form" action="buscar.php" method="get" role="search">
                        <div class="filter-wrapper">
                            <select name="sexo" class="filter-select filter-large" aria-label="Filtrar por género" onchange="this.form.submit()">
                                <option value="">Todos los géneros</option>
                                <?php foreach ($categorias_padres as $cat): ?>
                                    <option value="<?php echo (int)$cat['id_categoria']; ?>">
                                        <?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SEARCH 3: Category Search -->
            <div class="search-banner search-banner-category">
                <div class="search-content">
                    <h3 class="search-title">Filtrar por Categoría</h3>
                    <form class="search-form" action="buscar.php" method="get" role="search">
                        <div class="filter-wrapper">
                            <select name="categoria" class="filter-select filter-large" aria-label="Filtrar por categoría" onchange="this.form.submit()">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias_sub as $cat): ?>
                                    <option value="<?php echo (int)$cat['id_categoria']; ?>">
                                        <?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <span>Hola <?php echo $_SESSION['nombre']; ?></span>
            <button class="close-cart" id="closeCart">&times;</button>
        </div>
        <div class="cart-items" id="cartItems">
            <?php if (empty($carrito)): ?>
                <div class="empty-cart">
                    <p>Tu carrito está vacío</p>
                </div>
            <?php else: ?>
                <?php foreach ($carrito as $item): ?>
                    <?php $total_carrito += $item['precio'] * $item['cantidad']; ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name"><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?></div>
                            <div class="cart-item-price">$<?php echo number_format((float)$item['precio'], 2); ?></div>
                        </div>
                        <div class="cart-item-quantity">x<?php echo (int)$item['cantidad']; ?></div>
                        <button class="cart-remove" data-id="<?php echo (int)$item['id_producto']; ?>" title="Eliminar">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="sidebar-actions">
            <a href="direccion.php" class="sidebar-btn">Gestionar Direcciones</a>
            <a href="../backend/cerrarSesion.php" class="sidebar-btn sidebar-logout">Cerrar Sesión</a>
        </div>
        <?php if (!empty($carrito)): ?>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total_carrito, 2); ?></span>
                </div>
                <button class="btn-checkout">Proceder al Pago</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <h1>Estilo que te define</h1>
            <p>Descubre la mejor colección de ropa casual y cómoda</p>
            <button class="btn-primary" onclick="document.getElementById('camisas').scrollIntoView({behavior: 'smooth'})">
                Explorar Colección
            </button>
        </div>
        <div class="hero-image">
            <img src="../img/presentacion2.jpg" alt="Modelo con ropa casual">
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <h2>Nuestras Categorías</h2>
        <div class="categories-grid">
            <div class="category-card" onclick="document.getElementById('camisas').scrollIntoView({behavior: 'smooth'})">
                <img src="/placeholder.svg?height=300&width=300" alt="Camisas">
                <h3>Camisas</h3>
                <p>Cómodas y versátiles</p>
            </div>
            <div class="category-card" onclick="document.getElementById('pantalones').scrollIntoView({behavior: 'smooth'})">
                <img src="/placeholder.svg?height=300&width=300" alt="Pantalones">
                <h3>Pantalones</h3>
                <p>Ajuste perfecto</p>
            </div>
            <div class="category-card" onclick="document.getElementById('tenis').scrollIntoView({behavior: 'smooth'})">
                <img src="/placeholder.svg?height=300&width=300" alt="Tenis">
                <h3>Tenis</h3>
                <p>Comodidad total</p>
            </div>
        </div>
    </section>

    <!-- Camisas Section -->
    <?php
    $camisas = [];
    if (isset($conn) && $conn) {
        $sql = "SELECT id_producto, nombre, precio, imagen FROM productos WHERE categoria = 5 LIMIT 4";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $camisas[] = $row;
            }
            mysqli_free_result($res);
        } else {
            $res2 = mysqli_query($conn, "SELECT id_producto, nombre, precio, imagen FROM productos ORDER BY id_producto LIMIT 4");
            if ($res2) {
                while ($row = mysqli_fetch_assoc($res2)) {
                    $camisas[] = $row;
                }
                mysqli_free_result($res2);
            }
        }
    }
    ?>
    <section class="products-section" id="camisas">
        <h2>Camisas</h2>
        <div class="products-grid">
            <?php if (!empty($camisas)): ?>
                <?php foreach ($camisas as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php $img = !empty($producto['imagen']) ? $producto['imagen'] : '/placeholder.svg?height=280&width=280'; ?>
                            <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?></h3>
                        <p class="price"><?php echo isset($producto['precio']) ? '$' . number_format((float)$producto['precio'], 2) : 'Precio no disponible'; ?></p>
                        <a href="../backend/agregarCarrito.php?id_producto=<?php echo (int)$producto['id_producto']; ?>" class="btn-add-cart" role="button">Agregar al Carrito</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Pantalones Section -->
    <?php
    $pantalones = [];
    if (isset($conn) && $conn) {
        $sql = "SELECT id_producto, nombre, precio, imagen FROM productos WHERE categoria = 6 LIMIT 4";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $pantalones[] = $row;
            }
            mysqli_free_result($res);
        } else {
            $res2 = mysqli_query($conn, "SELECT id_producto, nombre, precio, imagen FROM productos ORDER BY id_producto LIMIT 4");
            if ($res2) {
                while ($row = mysqli_fetch_assoc($res2)) {
                    $pantalones[] = $row;
                }
                mysqli_free_result($res2);
            }
        }
    }
    ?>
    <section class="products-section" id="pantalones">
        <h2>Pantalones</h2>
        <div class="products-grid">
            <?php if (!empty($pantalones)): ?>
                <?php foreach ($pantalones as $producto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php $img = !empty($producto['imagen']) ? $producto['imagen'] : '/placeholder.svg?height=280&width=280'; ?>
                            <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?></h3>
                        <p class="price"><?php echo isset($producto['precio']) ? '$' . number_format((float)$producto['precio'], 2) : 'Precio no disponible'; ?></p>
                        <a href="../backend/agregarCarrito.php?id_producto=<?php echo (int)$producto['id_producto']; ?>" class="btn-add-cart" role="button">Agregar al Carrito</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Tenis Section -->
    <section class="products-section" id="tenis">
        <h2>Tenis</h2>
        <div class="products-grid">
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Tenis Blanco">
                    <span class="badge">Bestseller</span>
                </div>
                <h3>Tenis Blanco Deportivo</h3>
                <p class="price">$89.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Tenis Negro">
                </div>
                <h3>Tenis Negro Corrida</h3>
                <p class="price">$94.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Tenis Gris">
                    <span class="badge">-10%</span>
                </div>
                <h3>Tenis Gris Casual</h3>
                <p class="price"><span class="original-price">$79.99</span> $71.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Tenis Rojo">
                </div>
                <h3>Tenis Rojo Moderno</h3>
                <p class="price">$85.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="newsletter-content">
            <h2>Suscríbete a nuestro newsletter</h2>
            <p>Recibe ofertas exclusivas y novedades de moda</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" placeholder="Tu correo electrónico" required>
                <button type="submit" class="btn-primary">Suscribirse</button>
            </form>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer" id="contacto">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre Nosotros</h4>
                <p>Shopware es tu destino para ropa casual de calidad a precios accesibles.</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="#camisas">Camisas</a></li>
                    <li><a href="#pantalones">Pantalones</a></li>
                    <li><a href="#tenis">Tenis</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>Email: info@Shopware.com</p>
                <p>Teléfono: +1 (555) 123-4567</p>
            </div>
            <div class="footer-section">
                <h4>Síguenos</h4>
                <div class="social-links">
                    <a href="#" class="social-link">Instagram</a>
                    <a href="#" class="social-link">Facebook</a>
                    <a href="#" class="social-link">Twitter</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Shopware. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- JavaScript for Cart Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cartToggle = document.getElementById('cartToggle');
            const cartSidebar = document.getElementById('cartSidebar');
            const cartOverlay = document.getElementById('cartOverlay');
            const closeCart = document.getElementById('closeCart');

            function openCart() {
                cartSidebar.classList.add('active');
                cartOverlay.classList.add('active');
            }
            function closeCartFn() {
                cartSidebar.classList.remove('active');
                cartOverlay.classList.remove('active');
            }

            if (cartToggle) cartToggle.addEventListener('click', openCart);
            if (closeCart) closeCart.addEventListener('click', closeCartFn);
            if (cartOverlay) cartOverlay.addEventListener('click', closeCartFn);
        });
    </script>
</body>
</html>
