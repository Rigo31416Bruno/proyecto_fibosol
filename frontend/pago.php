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

if (empty($carrito_items)) {
    header('Location: carrito.php');
    exit();
}

// Obtener dirección del usuario desde la base de datos
$direccion_usuario = '';
if (isset($conn) && $conn) {
    $sqlAddr = "SELECT Direccion FROM usuarios WHERE ID_Usuario = ? LIMIT 1";
    $stmtAddr = $conn->prepare($sqlAddr);
    if ($stmtAddr) {
        $stmtAddr->bind_param('i', $id_usuario);
        $stmtAddr->execute();
        $resAddr = $stmtAddr->get_result();
        if ($resAddr && $resAddr->num_rows > 0) {
            $rowAddr = $resAddr->fetch_assoc();
            $direccion_usuario = $rowAddr['Direccion'];
        }
        $stmtAddr->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Shopware</title>
    <link rel="stylesheet" href="../styles/inicio.css">
    <link rel="stylesheet" href="../styles/pago.css">
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

    <div class="payment-container">
        <div class="payment-content">
            <div class="payment-header">
                <h1>Finalizar Compra</h1>
                <div class="steps-indicator">
                    <div class="step completed">
                        <span class="step-number">1</span>
                        <span class="step-label">Carrito</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step active">
                        <span class="step-number">2</span>
                        <span class="step-label">Pago</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <span class="step-label">Confirmación</span>
                    </div>
                </div>
            </div>

            <div class="payment-layout">
                <!-- Formulario de Pago -->
                <div class="payment-form-section">
                    <div class="form-card">
                        <div class="form-header">
                            <h2>Información de Pago</h2>
                            <div class="secure-badge">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span>Pago Seguro</span>
                            </div>
                        </div>

                        <form id="paymentForm" class="payment-form">
                            <!-- Tarjetas aceptadas -->
                            <div class="accepted-cards">
                                <img src="/placeholder.svg?height=30&width=48" alt="Visa">
                                <img src="/placeholder.svg?height=30&width=48" alt="Mastercard">
                                <img src="/placeholder.svg?height=30&width=48" alt="American Express">
                            </div>

                
                            <div class="form-group">
                                <label for="cardNumber">Número de Tarjeta</label>
                                <div class="input-wrapper">
                                    <input 
                                        type="text" 
                                        id="cardNumber" 
                                        name="cardNumber" 
                                        placeholder="1234 5678 9012 3456"
                                        maxlength="19"
                                        required
                                    >
                                    <span class="card-icon">💳</span>
                                </div>
                                <span class="error-message" id="cardNumberError"></span>
                            </div>

                
                            <div class="form-group">
                                <label for="cardHolder">Nombre del Titular</label>
                                <input 
                                    type="text" 
                                    id="cardHolder" 
                                    name="cardHolder" 
                                    placeholder="Como aparece en la tarjeta"
                                    required
                                >
                                <span class="error-message" id="cardHolderError"></span>
                            </div>

                
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry">Fecha de Vencimiento</label>
                                    <input 
                                        type="text" 
                                        id="expiry" 
                                        name="expiry" 
                                        placeholder="MM/AA"
                                        maxlength="5"
                                        required
                                    >
                                    <span class="error-message" id="expiryError"></span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cvv">
                                        CVV
                                        <span class="cvv-tooltip" title="Código de 3 dígitos en el reverso de tu tarjeta">ⓘ</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="cvv" 
                                        name="cvv" 
                                        placeholder="123"
                                        maxlength="4"
                                        required
                                    >
                                    <span class="error-message" id="cvvError"></span>
                                </div>
                            </div>

                
                            <div class="form-section-divider">
                                <h3>Dirección de Facturación</h3>
                            </div>

                            <div class="form-group">
                                <label>Dirección de Facturación</label>
                                <div class="input-wrapper">
                                    <p class="read-only-address" style="margin:0;padding:8px;background:#f7f7f7;border-radius:4px;"><?php echo htmlspecialchars($direccion_usuario ?: 'No hay dirección registrada.'); ?></p>
                                </div>
                                <input type="hidden" id="billingAddress" name="billingAddress" value="<?php echo htmlspecialchars($direccion_usuario, ENT_QUOTES); ?>">
                                <input type="hidden" id="city" name="city" value="">
                                <input type="hidden" id="zipCode" name="zipCode" value="">
                            </div>


                            <!-- Botón de Pago -->
                            <button type="submit" class="btn-pay">
                                <span class="btn-text">Pagar $<?php echo number_format($total_carrito, 2); ?></span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner"></span>
                                    Procesando...
                                </span>
                            </button>

                            <div class="payment-security">
                                <p>🔒 Tu información está protegida con encriptación SSL de 256 bits</p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resumen del Pedido -->
                <div class="order-summary-section">
                    <div class="summary-card">
                        <h3>Resumen del Pedido</h3>
                        
                        <div class="summary-items">
                            <?php foreach ($carrito_items as $item): ?>
                                <div class="summary-item">
                                    <div class="item-image-small">
                                        <?php $img = !empty($item['imagen']) ? $item['imagen'] : '/placeholder.svg?height=60&width=60'; ?>
                                        <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?>">
                                    </div>
                                    <div class="item-details-small">
                                        <h4><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?></h4>
                                        <p>Talla: <?php echo htmlspecialchars($item['talla'], ENT_QUOTES); ?> × <?php echo (int)$item['cantidad']; ?></p>
                                    </div>
                                    <div class="item-price-small">
                                        $<?php echo number_format((float)$item['subtotal'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-totals">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($total_carrito, 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Envío</span>
                                <span class="free-shipping">Gratis</span>
                            </div>
                            <div class="summary-row">
                                <span>IVA (16%)</span>
                                <span>$<?php echo number_format($total_carrito * 0.16, 2); ?></span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-total">
                                <span>Total a Pagar</span>
                                <span class="total-amount">$<?php echo number_format($total_carrito * 1.16, 2); ?></span>
                            </div>
                        </div>

                        <div class="trust-badges">
                            <div class="trust-badge">
                                <span>✓</span>
                                <span>Envío Gratis</span>
                            </div>
                            <div class="trust-badge">
                                <span>✓</span>
                                <span>Devolución 30 días</span>
                            </div>
                            <div class="trust-badge">
                                <span>✓</span>
                                <span>Garantía de calidad</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Modales mejorados con diseño moderno -->
    <!-- Modal de Confirmación de Pago -->
    <div id="confirmPaymentModal" class="modal-overlay">
        <div class="modal-container modal-confirm">
            <div class="modal-icon modal-icon-warning">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h3 class="modal-title">Confirmar Pago</h3>
            <p class="modal-message">Estás a punto de realizar un pago de <strong class="highlight-amount">$<?php echo number_format($total_carrito * 1.16, 2); ?></strong></p>
            <p class="modal-submessage">¿Deseas continuar con esta transacción?</p>
            <div class="modal-actions">
                <button id="confirmPaymentBtn" class="btn-modal btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Sí, Procesar Pago
                </button>
                <button id="cancelPaymentBtn" class="btn-modal btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Éxito -->
    <div id="paymentSuccessModal" class="modal-overlay">
        <div class="modal-container modal-success">
            <div class="modal-icon modal-icon-success">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="16 8 10 14 8 12"></polyline>
                </svg>
            </div>
            <h3 class="modal-title">¡Pago Realizado!</h3>
            <p class="modal-message" id="successMessage">Tu pago ha sido procesado correctamente</p>
            <div class="success-details">
                <div class="success-item">
                    <span class="success-label">Total Pagado:</span>
                    <span class="success-value">$<?php echo number_format($total_carrito * 1.16, 2); ?></span>
                </div>
            </div>
            <div class="modal-actions">
                <button id="generateInvoiceBtn" class="btn-modal btn-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Generar Factura PDF
                </button>
                <a href="inicio.php" class="btn-modal btn-outline">Volver al Inicio</a>
            </div>
        </div>
    </div>

    <!-- Modal de Error -->
    <div id="errorModal" class="modal-overlay">
        <div class="modal-container modal-error">
            <div class="modal-icon modal-icon-error">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <h3 class="modal-title">Error en el Pago</h3>
            <p class="modal-message" id="errorMessage">Ha ocurrido un error al procesar tu pago</p>
            <div class="modal-actions">
                <button id="closeErrorBtn" class="btn-modal btn-danger">Entendido</button>
            </div>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="../js/pago.js"></script>
</body>
</html>
