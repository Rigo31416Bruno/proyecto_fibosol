<?php
session_start();
require_once '../db/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header('Location: ../frontend/inicio_de_sesion.html');
    exit();
}

$busqueda = $_GET['buscar'] ?? '';
$categoria_filtro = $_GET['categoria'] ?? '';
$sexo = $_GET['sexo'] ?? '';
$orden = $_GET['orden'] ?? 'id_producto';

$allowed_orders = ['id_producto', 'precio', 'nombre'];
if (!in_array($orden, $allowed_orders)) {
    $orden = 'id_producto';
}

$query = "SELECT p.*, c.nombre as categoria_nombre, 
          COALESCE(SUM(pt.stock), 0) as stock_total
          FROM productos p 
          LEFT JOIN categorias c ON p.categoria = c.id_categoria
          LEFT JOIN producto_tallas pt ON p.id_producto = pt.id_producto
          WHERE 1=1";

$params = [];
if ($busqueda) {
    $query .= " AND p.nombre LIKE ?";
    $params[] = "%$busqueda%";
}

if ($categoria_filtro) {
    $query .= " AND p.categoria = ?";
    $params[] = $categoria_filtro;
}

if ($sexo && in_array($sexo, ['1','2'])) {
    $query .= " AND (c.id_categoria = ? OR c.ParentID = ? )";
    $params[] = $sexo;
    $params[] = $sexo;
}

$query .= " GROUP BY p.id_producto ORDER BY $orden DESC";

$productos = [];
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $bind_names = [];
        $bind_names[] = $stmt;
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = & $params[$i];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_names);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $productos[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Categorías para filtros y formularios
$categorias = [];
if (!empty($sexo) && in_array($sexo, ['1','2'])) {
    $sexo_int = intval($sexo);
    $res_cat = mysqli_query($conn, "SELECT * FROM categorias WHERE ParentID = $sexo_int");
} else {
    $res_cat = mysqli_query($conn, "SELECT * FROM categorias WHERE ParentID IS NOT NULL");
}
if ($res_cat) {
    while ($c = mysqli_fetch_assoc($res_cat)) {
        $categorias[] = $c;
    }
}

// Tallas para el formulario de nuevo producto
$tallas_query = mysqli_query($conn, "SELECT * FROM tallas ORDER BY orden ASC");
$tallas = [];
if ($tallas_query) {
    while ($t = mysqli_fetch_assoc($tallas_query)) {
        $tallas[] = $t;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Admin</title>
    <link rel="stylesheet" href="../styles/administrador.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="content-header">
                <div>
                    <h2>Gestión de Productos</h2>
                    <p class="header-subtitle">Administra el catálogo de productos de la tienda</p>
                </div>
                <button class="btn btn-primary" id="btnNuevoProducto">
                    <svg id="iconNuevoProducto" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.3s ease;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span id="textNuevoProducto">Nuevo Producto</span>
                </button>
            </header>

            <!-- Formulario desplegable para nuevo producto -->
            <div id="formNuevoProducto" class="form-nuevo-producto">
                <div class="form-header">
                    <h3>Agregar Nuevo Producto</h3>
                    <p>Completa la información del producto</p>
                </div>
                <form id="nuevoProductoForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nuevo_nombre">Nombre del Producto *</label>
                            <input type="text" id="nuevo_nombre" name="nombre" class="form-input" placeholder="Ej: Camisa Negra Simple" required>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_precio">Precio *</label>
                            <div class="input-with-icon">
                                <span class="input-icon">$</span>
                                <input type="number" step="0.01" id="nuevo_precio" name="precio" class="form-input" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_categoria">Categoría *</label>
                            <select id="nuevo_categoria" name="categoria" class="form-select" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>">
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_imagen">Imagen</label>
                            <input type="file" id="nuevo_imagen" name="imagen" accept="image/*" class="form-input">
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Stock por Talla</h4>
                        <div class="tallas-grid">
                            <?php foreach ($tallas as $talla): ?>
                            <div class="talla-item">
                                <label for="stock_<?php echo $talla['id_talla']; ?>"><?php echo htmlspecialchars($talla['nombre']); ?></label>
                                <input type="number"
                                       id="stock_<?php echo $talla['id_talla']; ?>"
                                       name="stock[<?php echo $talla['id_talla']; ?>]"
                                       class="form-input"
                                       placeholder="0"
                                       min="0"
                                       value="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-modal btn-outline" id="cancelarNuevoBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            Cancelar
                        </button>
                        <button type="submit" class="btn-modal btn-success">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filtros y buscador -->
            <div class="content-section">
                <form method="GET" action="">
                    <div class="filters-row">
                        <div class="search-bar filter-group" style="display:flex; gap:0.5rem; align-items:center;">
                            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            <input type="text" name="buscar" class="form-input" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($busqueda); ?>">
                            <select name="sexo" class="form-select">
                                <option value="" <?php echo $sexo === '' ? 'selected' : ''; ?>>Todos</option>
                                <option value="1" <?php echo $sexo === '1' ? 'selected' : ''; ?>>Hombre</option>
                                <option value="2" <?php echo $sexo === '2' ? 'selected' : ''; ?>>Mujer</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="categoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $categoria_filtro == $cat['id_categoria'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="orden" class="form-select">
                                <option value="id_producto" <?php echo $orden == 'id_producto' ? 'selected' : ''; ?>>Más recientes</option>
                                <option value="precio" <?php echo $orden == 'precio' ? 'selected' : ''; ?>>Precio mayor</option>
                                <option value="nombre" <?php echo $orden == 'nombre' ? 'selected' : ''; ?>>Nombre A-Z</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Tabla de productos -->
            <div class="content-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">No se encontraron productos</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><span class="badge">#<?php echo $producto['id_producto']; ?></span></td>
                                    <td>
                                        <?php if ($producto['imagen']): ?>
                                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="" class="image-preview">
                                        <?php else: ?>
                                        <div class="image-placeholder">Sin imagen</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                                    <td class="price">$<?php echo number_format($producto['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge" style="color: <?php echo $producto['stock_total'] <= 5 ? 'var(--accent-red)' : 'var(--accent-green)'; ?>">
                                            <?php echo $producto['stock_total']; ?> unidades
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" class="action-btn action-btn-edit" 
                                                    data-id="<?php echo $producto['id_producto']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>" 
                                                    data-price="<?php echo $producto['precio']; ?>" 
                                                    data-category="<?php echo $producto['categoria']; ?>" 
                                                    data-image="<?php echo htmlspecialchars($producto['imagen']); ?>">Editar</button>
                                            <button class="action-btn action-btn-delete" 
                                                    data-id="<?php echo $producto['id_producto']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>">Eliminar</button>
                                        </div>
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

    <!-- Modales -->
    <div id="simpleModal" class="modal-overlay">
        <div class="modal-container">
            <p id="simpleModalMessage" style="margin:0; font-size:1rem;"></p>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                <button id="simpleCancelBtn" class="btn-modal btn-outline">Cancelar</button>
                <button id="simpleConfirmBtn" class="btn-modal btn-danger">Eliminar</button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-container" style="max-width:520px;">
            <h3 style="margin:0 0 0.75rem 0;">Editar Producto</h3>
            <form id="editProductForm">
                <input type="hidden" name="id_producto" id="edit_id">
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    <label>Nombre</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-input">
                    <label>Precio</label>
                    <input type="number" step="0.01" id="edit_precio" name="precio" class="form-input">
                    <label>Categoría</label>
                    <select id="edit_categoria" name="categoria" class="form-select">
                        <option value="">-- Ninguna --</option>
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Imagen</label>
                    <div style="display:flex; gap:0.5rem; align-items:center;">
                        <input type="file" id="edit_imagen" name="imagen" accept="image/*" class="form-input">
                        <img id="edit_image_preview" src="" alt="Preview" style="width:60px;height:60px;object-fit:cover;border-radius:4px;display:none;">
                    </div>
                    <div class="form-section" style="margin-top:12px;">
                        <h4>Stock por Talla</h4>
                        <div class="tallas-grid" id="edit_tallas_grid">
                            <?php foreach ($tallas as $talla): ?>
                            <div class="talla-item">
                                <label for="edit_stock_<?php echo $talla['id_talla']; ?>"><?php echo htmlspecialchars($talla['nombre']); ?></label>
                                <input type="number"
                                       id="edit_stock_<?php echo $talla['id_talla']; ?>"
                                       name="stock[<?php echo $talla['id_talla']; ?>]"
                                       class="form-input"
                                       placeholder="0"
                                       min="0"
                                       value="0">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                    <button type="button" id="editCancelBtn" class="btn-modal btn-outline">Cancelar</button>
                    <button type="submit" class="btn-modal btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // === Toggle formulario nuevo producto ===
        document.getElementById('btnNuevoProducto').addEventListener('click', function () {
            const form = document.getElementById('formNuevoProducto');
            const icon = document.getElementById('iconNuevoProducto');
            const text = document.getElementById('textNuevoProducto');

            form.classList.toggle('active');

            if (form.classList.contains('active')) {
                icon.style.transform = 'rotate(45deg)';
                text.textContent = 'Cerrar Formulario';
            } else {
                icon.style.transform = 'rotate(0)';
                text.textContent = 'Nuevo Producto';
            }
        });

        document.getElementById('cancelarNuevoBtn').addEventListener('click', function () {
            document.getElementById('formNuevoProducto').classList.remove('active');
            document.getElementById('iconNuevoProducto').style.transform = 'rotate(0)';
            document.getElementById('textNuevoProducto').textContent = 'Nuevo Producto';
        });

        // === Toast de notificaciones ===
        function mostrarResultado(status, title, message) {
            let container = document.getElementById('adminToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'adminToastContainer';
                container.style.position = 'fixed';
                container.style.top = '1rem';
                container.style.right = '1rem';
                container.style.zIndex = 99999;
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gap = '0.5rem';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.style.minWidth = '220px';
            toast.style.maxWidth = '320px';
            toast.style.padding = '0.6rem 0.9rem';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
            toast.style.color = '#fff';
            toast.style.fontFamily = 'Inter, system-ui, Arial';
            toast.style.fontSize = '0.95rem';
            toast.style.display = 'flex';
            toast.style.flexDirection = 'column';
            toast.style.gap = '0.2rem';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 200ms ease, transform 200ms ease';
            toast.style.transform = 'translateY(-6px)';
            toast.style.background = status === 'success' 
                ? 'linear-gradient(90deg,#28a745,#2ecc71)' 
                : 'linear-gradient(90deg,#e63946,#ff6b6b)';

            const tTitle = document.createElement('strong');
            tTitle.textContent = title;
            const tMsg = document.createElement('div');
            tMsg.textContent = message;
            tMsg.style.opacity = '0.95';
            tMsg.style.fontWeight = '400';

            toast.appendChild(tTitle);
            toast.appendChild(tMsg);
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            });

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-6px)';
                setTimeout(() => container.removeChild(toast), 250);
            }, 3000);
        }

        // === Eliminación ===
        let productoIdEliminar = null;

        function confirmarEliminar(id, nombre) {
            productoIdEliminar = parseInt(id, 10);
            document.getElementById('simpleModalMessage').textContent = '¿Eliminar "' + nombre + '"?';
            document.getElementById('simpleModal').classList.add('show');
        }

        function cerrarSimpleModal() {
            document.getElementById('simpleModal').classList.remove('show');
            productoIdEliminar = null;
        }

        document.getElementById('simpleCancelBtn').addEventListener('click', cerrarSimpleModal);

        document.getElementById('simpleConfirmBtn').addEventListener('click', function() {
            if (!productoIdEliminar) return;

            $.ajax({
                url: 'admin_backend/eliminarProducto.php',
                type: 'POST',
                data: { id: productoIdEliminar },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const fila = document.querySelector(`button[data-id="${productoIdEliminar}"]`)?.closest('tr');
                        if (fila) fila.remove();
                        mostrarResultado('success', 'Eliminado', response.message || 'Producto eliminado correctamente.');
                    } else {
                        mostrarResultado('error', 'Error', response.message || 'No se pudo eliminar.');
                    }
                    cerrarSimpleModal();
                },
                error: function() {
                    mostrarResultado('error', 'Error', 'Error de conexión.');
                    cerrarSimpleModal();
                }
            });
        });

        // Delegación para botones eliminar
        document.querySelector('.table-container')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-btn-delete');
            if (btn) {
                confirmarEliminar(btn.dataset.id, btn.dataset.name || '');
            }
        });

        // === Edición ===
            document.querySelector('.table-container')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-btn-edit');
            if (!btn) return;
            const id = btn.dataset.id;
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = btn.dataset.name;
            document.getElementById('edit_precio').value = btn.dataset.price;
            document.getElementById('edit_categoria').value = btn.dataset.category;
            // reset file input and show preview if exists
            const preview = document.getElementById('edit_image_preview');
            const fileInput = document.getElementById('edit_imagen');
            try { fileInput.value = null; } catch(e) {}
            if (btn.dataset.image) {
                preview.src = btn.dataset.image;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
            // obtener stock actual via AJAX
            $.getJSON('admin_backend/obtenerProducto.php', { id_producto: id }, function(resp){
                if (resp && resp.success) {
                        const product = resp.product || {};
                        if (product.imagen) {
                            preview.src = product.imagen;
                            preview.style.display = 'block';
                        }
                    const stock = resp.stock || {};
                    // rellenar inputs de stock
                    for (const key in stock) {
                        const input = document.getElementById('edit_stock_' + key);
                        if (input) input.value = stock[key];
                    }
                    document.getElementById('editModal').classList.add('show');
                } else {
                    mostrarResultado('error', 'Error', resp?.message || 'No se pudo cargar datos');
                }
            }).fail(function(){
                mostrarResultado('error', 'Error', 'Error de conexión al cargar producto');
            });
        });

        document.getElementById('editCancelBtn').addEventListener('click', () => {
            document.getElementById('editModal').classList.remove('show');
        });

        $('#editProductForm').on('submit', function(ev) {
            ev.preventDefault();
            const form = document.getElementById('editProductForm');
            const fd = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            $.ajax({
                url: 'admin_backend/editarProducto.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success) {
                        mostrarResultado('success', 'Actualizado', resp.message || 'Producto actualizado');
                        const btnEl = document.querySelector(`.action-btn-edit[data-id="${$('#edit_id').val()}"]`);
                        if (btnEl) {
                            const row = btnEl.closest('tr');
                            const cols = row.querySelectorAll('td');
                            cols[2].textContent = $('#edit_nombre').val();
                            cols[4].textContent = '$' + parseFloat($('#edit_precio').val()).toFixed(2);
                            cols[3].textContent = $('#edit_categoria option:selected').text();
                        }
                        document.getElementById('editModal').classList.remove('show');
                        // refrescar página después de un pequeño retraso para que se vea la notificación
                        setTimeout(function(){ window.location.reload(); }, 700);
                    } else {
                        mostrarResultado('error', 'Error', resp?.message || 'No se pudo actualizar');
                    }
                },
                error: function() {
                    mostrarResultado('error', 'Error', 'Error de conexión');
                },
                complete: function() {
                    if (btn) btn.disabled = false;
                }
            });
        });

        // === Nuevo producto: enviar con FormData (archivo) ===
        $('#nuevoProductoForm').on('submit', function(ev) {
            ev.preventDefault();
            const form = document.getElementById('nuevoProductoForm');
            const btn = form.querySelector('button[type="submit"]');
            const fd = new FormData(form);

            // disable button
            if (btn) btn.disabled = true;

            $.ajax({
                url: 'admin_backend/agregarProducto.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success) {
                        mostrarResultado('success', 'Creado', resp.message || 'Producto creado');
                        // recargar para mostrar nuevo producto
                        setTimeout(function(){ window.location.reload(); }, 700);
                    } else {
                        mostrarResultado('error', 'Error', resp?.message || 'No se pudo crear el producto');
                        if (btn) btn.disabled = false;
                    }
                },
                error: function() {
                    mostrarResultado('error', 'Error', 'Error de conexión');
                    if (btn) btn.disabled = false;
                }
            });
        });

        // Cerrar modales al hacer clic fuera
        window.addEventListener('click', function(e) {
            const simple = document.getElementById('simpleModal');
            const edit = document.getElementById('editModal');
            if (e.target === simple) cerrarSimpleModal();
            if (e.target === edit) edit.classList.remove('show');
        });
    </script>
</body>
</html>