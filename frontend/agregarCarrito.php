<?php
include '../backend/validarSesion.php';
require('../db/conexion.php');

if(!validarSesion()) {
    header('Location: inicio_de_sesion.html');
    exit();
}

$id_producto = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id_producto == 0) {
    header('Location: inicio.php');
    exit();
}

$producto = null;
if (isset($conn) && $conn) {
    $sql = "SELECT id_producto, nombre, precio, imagen, categoria FROM productos WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$producto) {
    header('Location: inicio.php');
    exit();
}

$tallas = [];
if (isset($conn) && $conn) {
    $sql = "SELECT pt.id_producto_talla, pt.id_talla, t.nombre, t.orden, pt.stock 
            FROM producto_tallas pt 
            JOIN tallas t ON pt.id_talla = t.id_talla 
            WHERE pt.id_producto = ? 
            ORDER BY t.orden";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tallas[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?> - Shopware</title>
    <link rel="stylesheet" href="../styles/inicio.css">
    <link rel="stylesheet" href="../styles/producto-detalle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <li><a href="inicio.php#inicio">Inicio</a></li>
                <li><a href="inicio.php#camisas">Camisas</a></li>
                <li><a href="inicio.php#pantalones">Pantalones</a></li>
                <li><a href="inicio.php#tenis">Tenis</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="inicio_de_sesion.html" class="btn-login">Inicia Sesión</a>
                <a href="registro.php" class="btn-register">Regístrate</a>
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



    <section class="product-detail-section">
        <div class="product-detail-container">
            <div class="product-detail-image">
                <?php $img = !empty($producto['imagen']) ? $producto['imagen'] : '/placeholder.svg?height=500&width=500'; ?>
                <img src="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>" class="detail-image">
            </div>

            <div class="product-detail-info">
                <h1 class="product-name"><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?></h1>
                
                <div class="product-price-section">
                    <p class="product-price">$<?php echo number_format((float)$producto['precio'], 2); ?></p>
                    <span class="product-availability">Disponible</span>
                </div>

                <form id="addToCartForm" class="product-form" method="POST" action="../backend/agregarCarrito.php">
                    <input type="hidden" name="id_producto" value="<?php echo (int)$producto['id_producto']; ?>">

                    
                    <div class="form-group">
                        <label for="sizeSelect" class="form-label">Selecciona una Talla</label>
                        <div class="sizes-grid">
                            <?php if (!empty($tallas)): ?>
                                <?php foreach ($tallas as $talla): ?>
                                    <label class="size-option">
                                        <input type="radio" name="id_talla" value="<?php echo (int)$talla['id_talla']; ?>" class="size-radio" data-stock="<?php echo (int)$talla['stock']; ?>" <?php echo ($talla['stock'] == 0) ? 'disabled aria-disabled="true"' : 'required'; ?>>
                                        <span class="size-label"><?php echo htmlspecialchars($talla['nombre'], ENT_QUOTES); ?></span>
                                        <?php if ($talla['stock'] == 0): ?>
                                            <span class="size-unavailable">Sin stock</span>
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-sizes">No hay tallas disponibles</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label for="quantitySelect" class="form-label">Cantidad</label>
                        <div class="quantity-wrapper">
                            <button type="button" class="qty-btn qty-minus" id="qtyMinus" aria-label="Disminuir cantidad">−</button>
                            <select id="quantitySelect" name="cantidad" class="quantity-select" required>
                                <option value="">Selecciona cantidad</option>
                            </select>
                            <button type="button" class="qty-btn qty-plus" id="qtyPlus" aria-label="Aumentar cantidad">+</button>
                        </div>
                        <p class="stock-info" id="stockInfo"></p>
                    </div>

                    <button type="submit" class="btn-add-to-cart" id="submitBtn">
                        Agregar al Carrito
                    </button>
                </form>

                <div class="product-description">
                    <h3>Descripción del Producto</h3>
                    <p><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?> - Prenda de excelente calidad, perfecta para tu estilo.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Sobre Nosotros</h4>
                <p>Shopware es tu destino para ropa casual de calidad.</p>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>Email: info@Shopware.com</p>
            </div>
        </div>
    </footer>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('addToCartForm');
                const sizeRadios = document.querySelectorAll('.size-radio');
                const quantitySelect = document.getElementById('quantitySelect');
                const qtyMinus = document.getElementById('qtyMinus');
                const qtyPlus = document.getElementById('qtyPlus');
                const stockInfo = document.getElementById('stockInfo');
                const submitBtn = document.getElementById('submitBtn');

                function clearQuantity() {
                        quantitySelect.innerHTML = '';
                        const placeholder = document.createElement('option');
                        placeholder.value = '';
                        placeholder.textContent = 'Selecciona cantidad';
                        quantitySelect.appendChild(placeholder);
                        quantitySelect.value = '';
                }

                function updateQuantityOptions() {
                        const selectedSize = document.querySelector('.size-radio:checked');
                        clearQuantity();

                        if (!selectedSize) {
                                quantitySelect.disabled = true;
                                stockInfo.textContent = 'Selecciona una talla primero';
                                submitBtn.disabled = true;
                                return;
                        }

                        const stock = parseInt(selectedSize.dataset.stock, 10) || 0;
                        if (stock <= 0) {
                                quantitySelect.disabled = true;
                                stockInfo.textContent = 'Sin stock disponible';
                                submitBtn.disabled = true;
                                return;
                        }

                        for (let i = 1; i <= stock; i++) {
                                const option = document.createElement('option');
                                option.value = String(i);
                                option.textContent = String(i);
                                quantitySelect.appendChild(option);
                        }

                        quantitySelect.disabled = false;
                        quantitySelect.min = 1;
                        quantitySelect.max = stock;
                        quantitySelect.value = '1';
                        stockInfo.textContent = `Stock disponible: ${stock}`;
                        submitBtn.disabled = false;
                }

                sizeRadios.forEach(radio => radio.addEventListener('change', updateQuantityOptions));

                qtyMinus.addEventListener('click', function(e) {
                        e.preventDefault();
                        let current = parseInt(quantitySelect.value, 10) || 1;
                        if (current > 1) {
                                quantitySelect.value = String(current - 1);
                                quantitySelect.dispatchEvent(new Event('change'));
                        }
                });

                qtyPlus.addEventListener('click', function(e) {
                        e.preventDefault();
                        let current = parseInt(quantitySelect.value, 10) || 0;
                        const max = parseInt(quantitySelect.max, 10) || parseInt(quantitySelect.options[quantitySelect.options.length - 1]?.value, 10) || 0;
                        if (current < max) {
                                quantitySelect.value = String(current + 1);
                                quantitySelect.dispatchEvent(new Event('change'));
                        }
                });

                quantitySelect.addEventListener('change', function() {
                        if (this.value) {
                                submitBtn.disabled = false;
                        } else {
                                submitBtn.disabled = true;
                        }
                });

                
                const initiallyChecked = document.querySelector('.size-radio:checked');
                if (initiallyChecked) {
                        updateQuantityOptions();
                } else {
                        clearQuantity();
                        quantitySelect.disabled = true;
                        submitBtn.disabled = true;
                }

                
                $(function () {
                    $('#addToCartForm').on('submit', function (e) {
                        e.preventDefault();

                        const $form = $(this);
                        const id_producto = $form.find('input[name="id_producto"]').val();
                        const id_talla = $form.find('input[name="id_talla"]:checked').val();
                        const cantidad = $form.find('select[name="cantidad"]').val();

                        if (!id_talla) {
                            showToast('Selecciona una talla.', 'warning');
                            return;
                        }
                        if (!cantidad) {
                            showToast('Selecciona la cantidad.', 'warning');
                            return;
                        }

                        const $submit = $form.find('#submitBtn');
                        $submit.prop('disabled', true).text('Agregando...');

                        $.ajax({
                            url: '../backend/insertarCarrito.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id_producto: id_producto,
                                id_talla: id_talla,
                                cantidad: cantidad
                            },
                            success: function (res) {
                                    if (res && res.success) {
                                    if (typeof res.cartCount !== 'undefined') {
                                        $('#cartCount').text(res.cartCount);
                                    } else {
                                        const $count = $('#cartCount');
                                        const current = parseInt($count.text()) || 0;
                                        $count.text(current + parseInt(cantidad, 10));
                                    }
                                    showToast(res.message || 'Producto agregado al carrito.', 'success');
                                    // Actualizar stock en la página para la talla seleccionada
                                    try {
                                        const selRadio = document.querySelector('input[name="id_talla"][value="' + id_talla + '"]');
                                        if (selRadio) {
                                            const cur = parseInt(selRadio.dataset.stock || '0', 10);
                                            const dec = parseInt(cantidad, 10) || 0;
                                            let nuevo = cur - dec;
                                            if (nuevo < 0) nuevo = 0;
                                            selRadio.dataset.stock = String(nuevo);
                                            // si stock llega a 0, deshabilitar y mostrar etiqueta
                                            if (nuevo === 0) {
                                                selRadio.disabled = true;
                                                selRadio.setAttribute('aria-disabled', 'true');
                                                const parent = selRadio.closest('.size-option');
                                                if (parent && !parent.querySelector('.size-unavailable')) {
                                                    const badge = document.createElement('span');
                                                    badge.className = 'size-unavailable';
                                                    badge.textContent = 'Sin stock';
                                                    parent.appendChild(badge);
                                                }
                                                // si estaba seleccionada, desmarcar y actualizar UI
                                                if (selRadio.checked) {
                                                    selRadio.checked = false;
                                                    // limpiar cantidad
                                                    quantitySelect.innerHTML = '';
                                                    const placeholder = document.createElement('option');
                                                    placeholder.value = '';
                                                    placeholder.textContent = 'Selecciona cantidad';
                                                    quantitySelect.appendChild(placeholder);
                                                    quantitySelect.value = '';
                                                    quantitySelect.disabled = true;
                                                    submitBtn.disabled = true;
                                                    stockInfo.textContent = 'Selecciona una talla primero';
                                                }
                                            } else {
                                                // si aún hay stock y la talla está seleccionada, actualizar cantidad disponible
                                                const selected = document.querySelector('.size-radio:checked');
                                                if (selected && selected.value === id_talla) {
                                                    // rebuild quantity options
                                                    quantitySelect.innerHTML = '';
                                                    for (let i = 1; i <= nuevo; i++) {
                                                        const opt = document.createElement('option');
                                                        opt.value = String(i);
                                                        opt.textContent = String(i);
                                                        quantitySelect.appendChild(opt);
                                                    }
                                                    quantitySelect.disabled = false;
                                                    quantitySelect.value = '1';
                                                    stockInfo.textContent = `Stock disponible: ${nuevo}`;
                                                    submitBtn.disabled = false;
                                                }
                                            }
                                        }
                                    } catch (e) { console.error('Error actualizando stock en UI', e); }
                                } else {
                                    showToast((res && res.message) || 'No se pudo agregar el producto.', 'error');
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (jqXHR.status === 401) {
                                    window.location.href = 'inicio_de_sesion.html';
                                    return;
                                }
                                const text = jqXHR.responseText ? jqXHR.responseText : (errorThrown || textStatus);
                                showToast('Error al agregar al carrito: ' + 'error');
                            },
                            complete: function () {
                                $submit.prop('disabled', false).text('Agregar al Carrito');
                            }
                        });
                    });
                });
        });
        </script>

        <script>
        function showToast(message, type = 'info', timeout = 3500) {
            const colors = { success: '#2e7d32', error: '#c62828', warning: '#f57c00', info: '#1565c0' };
            const toast = document.createElement('div');
            toast.className = 'site-toast';
            toast.style.position = 'fixed';
            toast.style.right = '20px';
            toast.style.bottom = '20px';
            toast.style.background = colors[type] || colors.info;
            toast.style.color = '#fff';
            toast.style.padding = '12px 16px';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 6px 24px rgba(0,0,0,0.2)';
            toast.style.zIndex = 12000;
            toast.style.fontWeight = '600';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 300ms';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, timeout);
        }
        </script>

        </script>
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
