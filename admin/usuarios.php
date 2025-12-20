<?php
session_start();
require_once '../db/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header('Location: ../frontend/inicio_de_sesion.html');
    exit();
}

$busqueda = $_GET['buscar'] ?? '';
$rol_filtro = $_GET['rol'] ?? '';
$orden = $_GET['orden'] ?? 'ID_Usuario';

$allowed_orders = ['ID_Usuario', 'Nombre', 'Correo'];
if (!in_array($orden, $allowed_orders)) {
    $orden = 'ID_Usuario';
}

$query = "SELECT u.*, r.NombreRol 
          FROM usuarios u 
          LEFT JOIN roles r ON u.ID_Rol = r.ID_Rol
          WHERE 1=1";

$params = [];
$types = '';

if ($busqueda) {
    $query .= " AND (u.Nombre LIKE ? OR u.Correo LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $types .= 'ss';
}

if ($rol_filtro) {
    $query .= " AND u.ID_Rol = ?";
    $params[] = $rol_filtro;
    $types .= 'i';
}

$query .= " ORDER BY $orden DESC";

$usuarios = [];
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    if (!empty($params)) {
        $bind_names = [];
        $bind_names[] = $stmt;
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bind_names);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $usuarios[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Obtener roles para el select
$roles = [];
$result_roles = mysqli_query($conn, "SELECT * FROM roles");
if ($result_roles) {
    while ($r = mysqli_fetch_assoc($result_roles)) {
        $roles[] = $r;
    }
}

// Estadísticas
$stats = [];
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
$row = mysqli_fetch_assoc($result);
$stats['total'] = $row['total'] ?? 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE ID_Rol = 1");
$row = mysqli_fetch_assoc($result);
$stats['clientes'] = $row['total'] ?? 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE ID_Rol = 2");
$row = mysqli_fetch_assoc($result);
$stats['admins'] = $row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin</title>
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
                    <h2>Gestión de Usuarios</h2>
                    <p class="header-subtitle">Administra los usuarios del sistema</p>
                </div>
                <button class="btn btn-primary" id="btnNuevoUsuario">
                    <svg id="iconNuevoUsuario" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.3s ease;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span id="textNuevoUsuario">Nuevo Usuario</span>
                </button>
            </header>

            <!-- Estadísticas -->
            <div class="stats-grid" style="margin-bottom: 24px;">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Usuarios</p>
                        <p class="stat-value"><?php echo $stats['total']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Clientes</p>
                        <p class="stat-value"><?php echo $stats['clientes']; ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l2 7h7l-5.5 4.5 2 7L12 16l-5.5 4.5 2-7L3 9h7z"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Administradores</p>
                        <p class="stat-value"><?php echo $stats['admins']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Formulario desplegable para nuevo usuario -->
            <div id="formNuevoUsuario" class="form-nuevo-producto">
                <div class="form-header">
                    <h3>Agregar Nuevo Usuario</h3>
                    <p>Completa la información del usuario</p>
                </div>
                <form id="nuevoUsuarioForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nuevo_nombre">Nombre Completo *</label>
                            <input type="text" id="nuevo_nombre" name="nombre" class="form-input" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_correo">Correo Electrónico *</label>
                            <input type="email" id="nuevo_correo" name="correo" class="form-input" placeholder="usuario@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_contrasena">Contraseña *</label>
                            <input type="password" id="nuevo_contrasena" name="contrasena" class="form-input" placeholder="Mínimo 6 caracteres" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="nuevo_rol">Rol *</label>
                            <select id="nuevo_rol" name="rol" class="form-select" required>
                                <option value="">Selecciona un rol</option>
                                <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['ID_Rol']; ?>">
                                    <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nuevo_telefono">Teléfono</label>
                            <input type="tel" id="nuevo_telefono" name="telefono" class="form-input" placeholder="1234567890" maxlength="10">
                        </div>
                        <div class="form-group">
                            <label for="nuevo_direccion">Dirección</label>
                            <input type="text" id="nuevo_direccion" name="direccion" class="form-input" placeholder="Calle, Colonia, Ciudad">
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
                            Guardar Usuario
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
                            <input type="text" name="buscar" class="form-input" placeholder="Buscar por nombre o correo..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <div class="filter-group">
                            <select name="rol" class="form-select">
                                <option value="">Todos los roles</option>
                                <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['ID_Rol']; ?>" <?php echo $rol_filtro == $rol['ID_Rol'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol['NombreRol']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="orden" class="form-select">
                                <option value="ID_Usuario" <?php echo $orden == 'ID_Usuario' ? 'selected' : ''; ?>>Más recientes</option>
                                <option value="Nombre" <?php echo $orden == 'Nombre' ? 'selected' : ''; ?>>Nombre A-Z</option>
                                <option value="Correo" <?php echo $orden == 'Correo' ? 'selected' : ''; ?>>Correo A-Z</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Tabla de usuarios -->
            <div class="content-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">No se encontraron usuarios</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><span class="badge">#<?php echo $usuario['ID_Usuario']; ?></span></td>
                                    <td><?php echo htmlspecialchars($usuario['Nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                                    <td>
                                        <span class="badge" style="background: <?php echo $usuario['ID_Rol'] == 2 ? 'var(--accent-purple)' : 'var(--accent-green)'; ?>; color: white;">
                                            <?php echo htmlspecialchars($usuario['NombreRol']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $usuario['Telefono'] ? htmlspecialchars($usuario['Telefono']) : '<span style="color: var(--text-muted);">Sin teléfono</span>'; ?></td>
                                    <td>
                                        <span class="badge" style="background: <?php echo $usuario['Estatus'] == 'activo' ? 'var(--accent-green)' : 'var(--accent-red)'; ?>; color: white;">
                                            <?php echo ucfirst($usuario['Estatus'] ?? 'activo'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" class="action-btn action-btn-edit" 
                                                    data-id="<?php echo $usuario['ID_Usuario']; ?>" 
                                                    data-nombre="<?php echo htmlspecialchars($usuario['Nombre'], ENT_QUOTES); ?>" 
                                                    data-correo="<?php echo htmlspecialchars($usuario['Correo'], ENT_QUOTES); ?>"
                                                    data-rol="<?php echo $usuario['ID_Rol']; ?>"
                                                    data-telefono="<?php echo htmlspecialchars($usuario['Telefono'] ?? '', ENT_QUOTES); ?>"
                                                    data-direccion="<?php echo htmlspecialchars($usuario['Direccion'] ?? '', ENT_QUOTES); ?>"
                                                    data-estatus="<?php echo htmlspecialchars($usuario['Estatus'] ?? 'activo', ENT_QUOTES); ?>">
                                                Editar
                                            </button>
                                            <?php if ($usuario['ID_Rol'] != 2): ?>
                                            <button class="action-btn action-btn-delete" 
                                                    data-id="<?php echo $usuario['ID_Usuario']; ?>" 
                                                    data-nombre="<?php echo htmlspecialchars($usuario['Nombre'], ENT_QUOTES); ?>">
                                                Eliminar
                                            </button>
                                            <?php else: ?>
                                            <button class="action-btn action-btn-delete" disabled title="No se puede eliminar un administrador" 
                                                    style="opacity:0.6;cursor:not-allowed;">
                                                Eliminar
                                            </button>
                                            <?php endif; ?>
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

    <!-- Modal de confirmación simple -->
    <div id="simpleModal" class="modal-overlay">
        <div class="modal-container">
            <p id="simpleModalMessage" style="margin:0; font-size:1rem;"></p>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                <button id="simpleCancelBtn" class="btn-modal btn-outline">Cancelar</button>
                <button id="simpleConfirmBtn" class="btn-modal btn-danger">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- Modal de edición -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-container" style="max-width:520px;">
            <h3 style="margin:0 0 0.75rem 0;">Editar Usuario</h3>
            <form id="editUsuarioForm">
                <input type="hidden" name="id_usuario" id="edit_id">
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    <label>Nombre</label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-input" required>
                    
                    <label>Correo</label>
                    <input type="email" id="edit_correo" name="correo" class="form-input" required>
                    
                    <label>Rol</label>
                    <select id="edit_rol" name="rol" class="form-select" required>
                        <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['ID_Rol']; ?>"><?php echo htmlspecialchars($rol['NombreRol']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label>Teléfono</label>
                    <input type="tel" id="edit_telefono" name="telefono" class="form-input" maxlength="10">
                    
                    <label>Dirección</label>
                    <input type="text" id="edit_direccion" name="direccion" class="form-input">
                    
                    <label>Estado</label>
                    <select id="edit_estatus" name="estatus" class="form-select">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                    
                    <label>Nueva Contraseña (dejar vacío para no cambiar)</label>
                    <input type="password" id="edit_contrasena" name="contrasena" class="form-input" placeholder="Opcional" minlength="6">
                </div>
                <div style="display:flex; gap:0.5rem; justify-content:flex-end; margin-top:1rem;">
                    <button type="button" id="editCancelBtn" class="btn-modal btn-outline">Cancelar</button>
                    <button type="submit" class="btn-modal btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // === Toggle formulario nuevo usuario ===
        document.getElementById('btnNuevoUsuario').addEventListener('click', function () {
            const form = document.getElementById('formNuevoUsuario');
            const icon = document.getElementById('iconNuevoUsuario');
            const text = document.getElementById('textNuevoUsuario');

            form.classList.toggle('active');

            if (form.classList.contains('active')) {
                icon.style.transform = 'rotate(45deg)';
                text.textContent = 'Cerrar Formulario';
            } else {
                icon.style.transform = 'rotate(0)';
                text.textContent = 'Nuevo Usuario';
            }
        });

        document.getElementById('cancelarNuevoBtn').addEventListener('click', function () {
            document.getElementById('formNuevoUsuario').classList.remove('active');
            document.getElementById('iconNuevoUsuario').style.transform = 'rotate(0)';
            document.getElementById('textNuevoUsuario').textContent = 'Nuevo Usuario';
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
        let usuarioIdEliminar = null;

        function confirmarEliminar(id, nombre) {
            usuarioIdEliminar = parseInt(id, 10);
            document.getElementById('simpleModalMessage').textContent = '¿Eliminar usuario "' + nombre + '"?';
            document.getElementById('simpleModal').classList.add('show');
        }

        function cerrarSimpleModal() {
            document.getElementById('simpleModal').classList.remove('show');
            usuarioIdEliminar = null;
        }

        document.getElementById('simpleCancelBtn').addEventListener('click', cerrarSimpleModal);

        document.getElementById('simpleConfirmBtn').addEventListener('click', function() {
            if (!usuarioIdEliminar) return;

            $.ajax({
                url: 'admin_backend/eliminarUsuario.php',
                type: 'POST',
                data: { id: usuarioIdEliminar },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const fila = document.querySelector(`button[data-id="${usuarioIdEliminar}"]`)?.closest('tr');
                        if (fila) fila.remove();
                        mostrarResultado('success', 'Eliminado', response.message || 'Usuario eliminado correctamente.');
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
                confirmarEliminar(btn.dataset.id, btn.dataset.nombre || '');
            }
        });

        // === Edición ===
        document.querySelector('.table-container')?.addEventListener('click', function(e) {
            const btn = e.target.closest('.action-btn-edit');
            if (!btn) return;
            
            document.getElementById('edit_id').value = btn.dataset.id;
            document.getElementById('edit_nombre').value = btn.dataset.nombre;
            document.getElementById('edit_correo').value = btn.dataset.correo;
            document.getElementById('edit_rol').value = btn.dataset.rol;
            document.getElementById('edit_telefono').value = btn.dataset.telefono;
            document.getElementById('edit_direccion').value = btn.dataset.direccion;
            document.getElementById('edit_estatus').value = btn.dataset.estatus;
            document.getElementById('edit_contrasena').value = '';
            
            document.getElementById('editModal').classList.add('show');
        });

        document.getElementById('editCancelBtn').addEventListener('click', () => {
            document.getElementById('editModal').classList.remove('show');
        });

        $('#editUsuarioForm').on('submit', function(ev) {
            ev.preventDefault();
            const form = document.getElementById('editUsuarioForm');
            const fd = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            $.ajax({
                url: 'admin_backend/editarUsuario.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success) {
                        mostrarResultado('success', 'Actualizado', resp.message || 'Usuario actualizado');
                        document.getElementById('editModal').classList.remove('show');
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

        // === Nuevo usuario ===
        $('#nuevoUsuarioForm').on('submit', function(ev) {
            ev.preventDefault();
            const form = document.getElementById('nuevoUsuarioForm');
            const btn = form.querySelector('button[type="submit"]');
            const fd = new FormData(form);

            if (btn) btn.disabled = true;

            $.ajax({
                url: 'admin_backend/agregarUsuario.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success) {
                        mostrarResultado('success', 'Creado', resp.message || 'Usuario creado');
                        setTimeout(function(){ window.location.reload(); }, 700);
                    } else {
                        mostrarResultado('error', 'Error', resp?.message || 'No se pudo crear el usuario');
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