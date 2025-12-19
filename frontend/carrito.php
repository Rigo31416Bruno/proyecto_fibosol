<?php
include '../backend/validarSesion.php';
require('../db/conexion.php');

if(!validarSesion())
{
    header('Location: inicio_de_sesion.html');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$carrito_items = [];
$total_carrito = 0;

if (isset($conn) && $conn) {
    $sql = "
        SELECT 
            c.id_carrito,
            c.id_producto,
            c.cantidad,
            p.nombre,
            p.precio,
            p.imagen,
            t.nombre as talla,
            (p.precio * c.cantidad) as subtotal
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        JOIN producto_tallas pt ON c.id_producto_talla = pt.id_producto_talla
        JOIN tallas t ON pt.id_talla = t.id_talla
        WHERE c.id_usuario = ?
        ORDER BY c.fecha_agregado DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $carrito_items[] = $row;
            $total_carrito += $row['subtotal'];
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - Shopware</title>
    <link rel="stylesheet" href="../styles/inicio.css">
    <link rel="stylesheet" href="../styles/carrito.css">
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
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="inicio.php#camisas">Camisas</a></li>
                <li><a href="inicio.php#pantalones">Pantalones</a></li>
                <li><a href="inicio.php#blusas">Blusas</a></li>
                <li><a href="inicio.php#contacto">Contacto</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="inicio_de_sesion.html" class="btn-login">Inicia Sesión</a>
                <a href="registro.php" class="btn-register">Regístrate</a>
            </div>
            <button id="cartToggle" class="cart-icon" aria-label="Abrir carrito">
                🛒 <span class="cart-count" id="cartCount"><?php echo count($carrito_items); ?></span>
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

    <section class="cart-page-container">
        <div class="cart-page-content">
            <h1 class="cart-title">Mi Carrito de Compras</h1>
            
            <?php if (empty($carrito_items)): ?>
                <div class="empty-cart-section">
                    <div class="empty-cart-icon">🛒</div>
                    <h2>Tu carrito está vacio</h2>
                    <p>Parece que aun no has agregado productos a tu carrito.</p>
                    <a href="inicio.php" class="btn-continue-shopping">Continuar Comprando</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <!-- Productos -->
                    <div class="cart-items-section">
                        <div class="cart-items-header">
                            <h2><?php echo count($carrito_items); ?> Producto<?php echo count($carrito_items) !== 1 ? 's' : ''; ?></h2>
                        </div>
                        
                        <div class="cart-items-list">
                            <?php foreach ($carrito_items as $item): ?>
                                <div class="cart-item-card" data-id="<?php echo (int)$item['id_carrito']; ?>" data-subtotal="<?php echo number_format((float)$item['subtotal'], 2, '.', ''); ?>">
                                    <div class="item-image">
                                        <?php $img = !empty($item['imagen']) ? $item['imagen'] : '/placeholder.svg?height=120&width=120'; ?>
                                        <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?>">
                                    </div>
                                    
                                    <div class="item-details">
                                        <h3><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?></h3>
                                        <div class="item-specs">
                                            <span class="spec">Talla: <strong><?php echo htmlspecialchars($item['talla'], ENT_QUOTES); ?></strong></span>
                                            <span class="spec">Cantidad: <strong><?php echo (int)$item['cantidad']; ?></strong></span>
                                        </div>
                                        <div class="item-price">
                                            <span class="unit-price">$<?php echo number_format((float)$item['precio'], 2); ?> c/u</span>
                                        </div>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <div class="item-subtotal">
                                            <span class="subtotal-label">Subtotal</span>
                                            <span class="subtotal-value">$<?php echo number_format((float)$item['subtotal'], 2); ?></span>
                                        </div>
                                        <button class="btn-remove" data-id="<?php echo (int)$item['id_carrito']; ?>" title="Eliminar del carrito">
                                            <span>×</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                
                    <div class="cart-summary-section">
                        <div class="summary-card">
                            <h3>Resumen de Compra</h3>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span id="summarySubtotal">$<?php echo number_format($total_carrito, 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Envío</span>
                                <span class="free-shipping">Gratis</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Descuento</span>
                                <span>$0.00</span>
                            </div>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-total">
                                <span>Total</span>
                                <span class="total-amount" id="summaryTotal">$<?php echo number_format($total_carrito, 2); ?></span>
                            </div>
                            
                            <a href="pago.php" class="btn-checkout-main">Proceder al Pago</a>
                            <a href="inicio.php" class="btn-continue-shopping-secondary">Continuar Comprando</a>
                            
                            <div class="security-badge">
                                <span>🔒</span>
                                <span>Compra segura</span>
                            </div>
                        </div>
                    </div>
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
                    <li><a href="inicio.php#camisas">Productos</a></li>
                    <li><a href="#">Contacto</a></li>
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

    <div id="confirmModal" class="confirm-modal" style="display:none; position:fixed; left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:9999;">
        <div style="background:#fff;padding:20px;max-width:400px;margin:auto;border-radius:6px;text-align:center;">
            <h3>Confirmar eliminación</h3>
            <p>¿Seguro que quieres eliminar este producto del carrito?</p>
            <div style="margin-top:16px;">
                <button id="confirmDelete" style="background:#c62828;color:#fff;border:none;padding:8px 12px;margin-right:8px;border-radius:4px;cursor:pointer;">Eliminar</button>
                <button id="cancelDelete" style="padding:8px 12px;border-radius:4px;cursor:pointer;">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const modal = document.getElementById('confirmModal');
        const confirmBtn = document.getElementById('confirmDelete');
        const cancelBtn = document.getElementById('cancelDelete');
        let selectedId = null;

        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                selectedId = this.dataset.id;
                if(modal) modal.style.display = 'flex';
            });
        });

        if(cancelBtn) cancelBtn.addEventListener('click', function(){ if(modal) modal.style.display='none'; selectedId=null; });

        if(confirmBtn) confirmBtn.addEventListener('click', function(){
            if(!selectedId) return;
            const fd = new FormData();
            fd.append('id_carrito', selectedId);
            fetch('../backend/eliminarCarrito.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if(data && data.success){
                    if(data.count === 0){
                        window.location.reload();
                        return;
                    }
                    const card = document.querySelector('.cart-item-card[data-id="' + selectedId + '"]');
                    if(card) card.remove();
                    const cartCount = document.getElementById('cartCount');
                    if(cartCount) cartCount.textContent = data.count;
                    const header = document.querySelector('.cart-items-header h2');
                    if(header) header.textContent = data.count + ' Producto' + (data.count !== 1 ? 's' : '');
                    const subtotalEl = document.getElementById('summarySubtotal');
                    const totalEl = document.getElementById('summaryTotal');
                    if(subtotalEl) subtotalEl.textContent = '$' + parseFloat(data.total).toFixed(2);
                    if(totalEl) totalEl.textContent = '$' + parseFloat(data.total).toFixed(2);
                    if(modal) modal.style.display = 'none';
                    selectedId = null;
                } else {
                    alert('No se pudo eliminar el producto. Intente de nuevo.');
                    if(modal) modal.style.display = 'none';
                }
            }).catch(err => { console.error(err); alert('Error de red'); if(modal) modal.style.display='none'; });
        });

        // Close modal on outside click
        window.addEventListener('click', function(e){ if(e.target === modal){ modal.style.display = 'none'; selectedId = null; } });
    });
    </script>

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

    <script src="../js/sidebar_funcionalidad.js"></script>
</body>
</html>
