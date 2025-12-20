<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../db/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['id_usuario']) || ($_SESSION['id_rol'] ?? null) != 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $parent = isset($_GET['parent']) ? $_GET['parent'] : '';

    $where = [];
    if ($q !== '') {
        $q_esc = mysqli_real_escape_string($conn, $q);
        $where[] = "c.nombre LIKE '%" . $q_esc . "%'";
    }
    if ($parent !== '') {
        $parent_id = intval($parent);
        $where[] = "c.ParentID = " . $parent_id;
    }

        $sql = "SELECT c.id_categoria, c.nombre, c.ParentID, p.nombre AS parent_name
            FROM categorias c
            LEFT JOIN categorias p ON c.ParentID = p.id_categoria";
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY c.nombre ASC';

    $res = mysqli_query($conn, $sql);
    if (!$res) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
        exit;
    }
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
    exit;

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON inválido']);
        exit;
    }

    $nombre = isset($input['nombre']) ? trim($input['nombre']) : '';
    $parent = isset($input['parent']) && $input['parent'] !== '' ? $input['parent'] : null;

    if (mb_strlen($nombre) < 2) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El nombre debe tener al menos 2 caracteres']);
        exit;
    }

    $nombre_esc = mysqli_real_escape_string($conn, $nombre);

    // Comprobar duplicado
    if ($parent === null) {
        $check_sql = "SELECT id_categoria FROM categorias WHERE nombre = '" . $nombre_esc . "' AND ParentID IS NULL LIMIT 1";
    } else {
        $parent_id = intval($parent);
        $check_sql = "SELECT id_categoria FROM categorias WHERE nombre = '" . $nombre_esc . "' AND ParentID = " . $parent_id . " LIMIT 1";
    }
    $chk = mysqli_query($conn, $check_sql);
    if ($chk && mysqli_num_rows($chk) > 0) {
        echo json_encode(['success' => false, 'message' => 'La categoría ya existe']);
        exit;
    }

    // Insertar
    if ($parent === null) {
        $insert_sql = "INSERT INTO categorias (nombre, ParentID) VALUES ('" . $nombre_esc . "', NULL)";
    } else {
        $parent_id = intval($parent);
        $insert_sql = "INSERT INTO categorias (nombre, ParentID) VALUES ('" . $nombre_esc . "', " . $parent_id . ")";
    }

    $ok = mysqli_query($conn, $insert_sql);
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al insertar categoría']);
        exit;
    }

    $new_id = mysqli_insert_id($conn);
    echo json_encode(['success' => true, 'id' => $new_id]);
    exit;

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
