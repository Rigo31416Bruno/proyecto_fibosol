<?php
include '../backend/validarSesion.php';
require('../db/conexion.php');

if(!validarSesion())
{
    header('Location: inicio_de_sesion.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleHub - Tu Tienda de Ropa</title>
    <link rel="stylesheet" href="../styles/inicio.css">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">StyleHub</div>
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
        </div>
    </nav>

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
            <img src="/placeholder.svg?height=500&width=500" alt="Modelo con ropa casual">
        </div>
    </section>

    <!-- Sección de Categorías -->
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

    <?php

$camisas = [];
if (isset($conn) && $conn) {
    // Intentar obtener 4 productos de la categoría "Camisas" (id_categoria = 3)
    $sql = "SELECT id_producto, nombre, precio, imagen FROM productos WHERE categoria = 5 LIMIT 4";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $camisas[] = $row;
        }
        mysqli_free_result($res);
    } else {
        // Si no hay resultados para la categoría, traer cualquier 4 productos como fallback
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
    <!-- Camisas -->
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

    <!-- Pantalones -->
    <section class="products-section" id="pantalones">
        <h2>Pantalones</h2>
        <div class="products-grid">
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Pantalón Jeans">
                </div>
                <h3>Pantalón Jeans Premium</h3>
                <p class="price">$59.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Pantalón Chino">
                </div>
                <h3>Pantalón Chino Clásico</h3>
                <p class="price">$54.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Pantalón Deportivo">
                    <span class="badge">-15%</span>
                </div>
                <h3>Pantalón Deportivo</h3>
                <p class="price"><span class="original-price">$49.99</span> $42.49</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
            <div class="product-card">
                <div class="product-image">
                    <img src="/placeholder.svg?height=280&width=280" alt="Pantalón Negro">
                </div>
                <h3>Pantalón Negro Ajustado</h3>
                <p class="price">$55.99</p>
                <button class="btn-add-cart">Agregar al Carrito</button>
            </div>
        </div>
    </section>

    <!-- Tenis -->
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

    <!-- Newsletter -->
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

    <!-- Footer -->
    <footer class="footer" id="contacto">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre Nosotros</h4>
                <p>StyleHub es tu destino para ropa casual de calidad a precios accesibles.</p>
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
                <p>Email: info@stylehub.com</p>
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
            <p>&copy; 2025 StyleHub. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>