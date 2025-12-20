<?php
session_start();
require_once '../db/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    header('Location: ../frontend/inicio_de_sesion.html');
    exit();
}

// Obtener lista de categorías para el select de "Parent" (opción para asignar padre al crear)
$parent_options = [];
$res = mysqli_query($conn, "SELECT id_categoria, nombre FROM categorias ORDER BY nombre ASC");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $parent_options[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Categorías - Admin</title>
    <link rel="stylesheet" href="../styles/administrador.css" />
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h2>Categorías</h2>
                <p class="header-subtitle">Buscar, agregar y ver categorías</p>
            </header>

            <!-- Buscadores -->
            <div class="content-section" style="margin-bottom:18px;">
                <div class="filters-row">
                    <div class="filter-group" style="flex:2;">
                        <div class="search-bar">
                            <span class="search-icon">🔎</span>
                            <input id="searchInput" type="text" class="form-input" placeholder="Buscar por nombre de categoría..." />
                        </div>
                    </div>
                    <div class="filter-group" style="flex:1;">
                        <select id="parentFilter" class="form-select">
                            <option value="">Todas las categorías (Parent)</option>
                            <?php foreach ($parent_options as $opt): ?>
                                <option value="<?php echo $opt['id_categoria']; ?>"><?php echo htmlspecialchars($opt['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;">
                        <button id="clearFilters" class="btn btn-outline" style="padding:10px 14px;background:transparent;border:1px solid var(--border-color);">Limpiar</button>
                    </div>
                </div>
            </div>

            <!-- Agregar categoría -->
            <div class="content-section form-nuevo-producto active" style="padding:20px;">
                <div class="form-header">
                    <h3>Agregar Categoría</h3>
                    <p>Solo nombre y (opcional) categoría padre.</p>
                </div>

                <div class="form-grid" style="margin-bottom:0;">
                    <div class="form-group">
                        <label for="newName">Nombre</label>
                        <input id="newName" type="text" class="form-input" placeholder="Ej: Camisas" />
                    </div>

                    <div class="form-group">
                        <label for="newParent">Parent (opcional)</label>
                        <select id="newParent" class="form-select">
                            <option value="">Ninguno</option>
                            <?php foreach ($parent_options as $opt): ?>
                                <option value="<?php echo $opt['id_categoria']; ?>"><?php echo htmlspecialchars($opt['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button id="addBtn" class="btn btn-primary">Agregar categoría</button>
                </div>
            </div>

            <!-- Lista de categorías -->
            <div class="content-section">
                <div class="section-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <h3>Listado de Categorías</h3>
                </div>

                <div class="table-container">
                    <table class="data-table" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Parent</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesBody">
                            <!-- Rellenado por JS -->
                            <tr><td colspan="3" class="empty-state">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<script>
    // Debounce util
    function debounce(fn, ms) {
      let t;
      return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), ms);
      };
    }

    async function loadCategories(q = '', parent = '') {
      const params = new URLSearchParams();
      if (q) params.append('q', q);
      if (parent) params.append('parent', parent);
      const res = await fetch('admin_backend/categorias_api.php?' + params.toString());
      if (!res.ok) {
        alert('Error al obtener categorías');
        return;
      }
      const data = await res.json();
      const tbody = document.getElementById('categoriesBody');
      tbody.innerHTML = '';
      if (!Array.isArray(data) || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="empty-state">No hay categorías</td></tr>';
        return;
      }
      for (const c of data) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><span class="badge">#${c.id_categoria}</span></td>
          <td>${escapeHtml(c.nombre)}</td>
          <td>${c.parent_name ? escapeHtml(c.parent_name) : '-'}</td>
        `;
        tbody.appendChild(tr);
      }
    }

    // Añadir categoría
    document.getElementById('addBtn').addEventListener('click', async (e) => {
      const name = document.getElementById('newName').value.trim();
      const parent = document.getElementById('newParent').value;
      if (name.length < 2) {
        alert('El nombre debe tener al menos 2 caracteres.');
        return;
      }
      const payload = { nombre: name, parent: parent || null };
      try {
        const res = await fetch('admin_backend/categorias_api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('newName').value = '';
          document.getElementById('newParent').value = '';
          // recargar filtros y tabla
          await refreshParentOptions(); // actualiza selects para incluir la nueva categoría
          await loadCategories(document.getElementById('searchInput').value.trim(), document.getElementById('parentFilter').value);
          alert('Categoría agregada correctamente.');
        } else {
          alert('Error: ' + (data.message || 'No se pudo agregar la categoría'));
        }
      } catch (err) {
        console.error(err);
        alert('Error al comunicarse con el servidor.');
      }
    });

    // Buscar en vivo
    const onSearch = debounce(() => {
      const q = document.getElementById('searchInput').value.trim();
      const parent = document.getElementById('parentFilter').value;
      loadCategories(q, parent);
    }, 300);

    document.getElementById('searchInput').addEventListener('input', onSearch);
    document.getElementById('parentFilter').addEventListener('change', onSearch);

    document.getElementById('clearFilters').addEventListener('click', () => {
      document.getElementById('searchInput').value = '';
      document.getElementById('parentFilter').value = '';
      loadCategories();
    });

    // Escape simple function for text nodes
    function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/[&<>"']/g, function(m) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]);
      });
    }

    // Refresca los options de parent en los selects (para que al agregar una categoría nueva aparezca)
    async function refreshParentOptions() {
      const res = await fetch('admin_backend/categorias_api.php');
      if (!res.ok) return;
      const data = await res.json();
      // reconstruir opciones
      const selects = [document.getElementById('parentFilter'), document.getElementById('newParent')];
      for (const s of selects) {
        const selectedValue = s.value;
        s.innerHTML = '';
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = s === document.getElementById('parentFilter') ? 'Todas las categorías (Parent)' : 'Ninguno';
        s.appendChild(emptyOption);
        for (const c of data) {
          const opt = document.createElement('option');
          opt.value = c.id_categoria;
          opt.textContent = c.nombre;
          s.appendChild(opt);
        }
        s.value = selectedValue || '';
      }
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', async () => {
      await refreshParentOptions();
      await loadCategories();
    });
</script>
</body>
</html>