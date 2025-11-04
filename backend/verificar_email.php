<?php
// devuelve HTML amigable para navegadores
// y sigue devolviendo JSON para peticiones AJAX/API.

$token = isset($_GET['token']) ? trim($_GET['token']) : null;
if (!$token || strlen($token) < 10) {
    http_response_code(400);
    $msg = 'Token inválido.';
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo renderPage('Token inválido', $msg, 'error');
    }
    exit;
}

try {
    require __DIR__ . '/../db/conexion.php';

    $dbObj = new Conexion();
    $conn = $dbObj->getConnection();

    $stmt = $conn->prepare('SELECT idCuenta, token_expires, correo_verificado FROM usuarios WHERE verification_token = ? LIMIT 1');
    if (!$stmt) throw new RuntimeException($conn->error);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($idCuenta, $token_expires, $correo_verificado);
    if (!$stmt->fetch()) {
        $stmt->close();
        http_response_code(404);
        $msg = 'Token no encontrado.';
        if (isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $msg]);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo renderPage('Token no encontrado', $msg, 'error');
        }
        exit;
    }
    $stmt->close();

    if ((int)$correo_verificado === 1) {
        http_response_code(200);
        $msg = 'Tu correo ya fue verificado anteriormente.';
        if (isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => $msg, 'verificado' => true]);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo renderPage('Correo ya verificado', $msg, 'success');
        }
        exit;
    }

    $now = new DateTime();
    $expires = new DateTime($token_expires);
    if ($expires < $now) {
        http_response_code(410);
        $msg = 'Token expirado. Solicita uno nuevo.';
        if (isAjax()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $msg]);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo renderPage('Token expirado', $msg, 'warning', true);
        }
        exit;
    }

    $upd = $conn->prepare('UPDATE usuarios SET correo_verificado = 1, verification_token = NULL, token_expires = NULL WHERE idCuenta = ?');
    if (!$upd) throw new RuntimeException('Prepare failed: ' . $conn->error);
    $upd->bind_param('i', $idCuenta);
    $ok = $upd->execute();
    $upd->close();

    if (!$ok) {
        throw new RuntimeException('No se pudo actualizar el usuario.');
    }

    http_response_code(200);
    $msg = '¡Correo verificado correctamente! Ya puedes iniciar sesión.';
    if (isAjax()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => $msg, 'verificado' => true]);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo renderPage('Verificación exitosa', $msg, 'success', false, true);
    }
    exit;

} catch (Exception $e) {
    // error_log($e->getMessage());
    http_response_code(500);
    $msg = 'Error de servidor. Intenta nuevamente más tarde.';
    if (isAjax()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo renderPage('Error', $msg, 'error');
    }
    exit;
}

// Detecta peticiones AJAX/API
function isAjax(): bool {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') return true;
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) return true;
    return false;
}

/**
 * Renderiza una página HTML simple y estilizada para mostrar el resultado.
 * $type: 'success' | 'error' | 'warning'
 * $showResend: si true muestra un botón para reenviar verificación (requiere implementar endpoint)
 * $showLogin: si true muestra botón para ir a login
 */
function renderPage(string $title, string $message, string $type = 'info', bool $showResend = false, bool $showLogin = false): string {
    $colors = [
        'success' => '#28a745',
        'error'   => '#dc3545',
        'warning' => '#ffc107',
        'info'    => '#0d6efd'
    ];
    $color = $colors[$type] ?? $colors['info'];
    $escTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $escMsg = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

    $appRoot = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
    $loginUrl = $appRoot . '/'; // ajustar si tienes ruta de login
    $resendUrl = $appRoot . '/frontend/verificacion.html'; // ejemplo: página para reintentar

    $actions = '';
    if ($showLogin) {
        $actions .= "<a class=\"btn btn-primary\" href=\"{$loginUrl}\">Ir a inicio / Iniciar sesión</a>";
    }
    if ($showResend) {
        $actions .= "<a class=\"btn btn-light\" href=\"{$resendUrl}\">Reenviar verificación</a>";
    }
    $actions .= '<a class="btn btn-light" href="javascript:window.close()">Cerrar</a>';

    return <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$escTitle}</title>
<style>
  body{font-family:Inter,Segoe UI,Roboto,Arial,sans-serif;background:#f4f6f9;margin:0;padding:40px;display:flex;align-items:center;justify-content:center;height:100vh}
  .card{width:100%;max-width:720px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(20,30,50,0.08);overflow:hidden}
  .header{padding:28px 32px;background:linear-gradient(90deg,{$color},rgba(0,0,0,0.02));color:#fff}
  .header h1{margin:0;font-size:20px;letter-spacing:0.2px}
  .body{padding:28px 32px;color:#1f2937}
  .message{font-size:16px;margin:0 0 20px;color:#374151}
  .actions{display:flex;gap:12px;flex-wrap:wrap}
  .btn{display:inline-block;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:600;cursor:pointer;border:0}
  .btn-primary{background:{$color};color:#fff}
  .btn-light{background:#f3f4f6;color:#111}
  .note{font-size:13px;color:#6b7280;margin-top:12px}
  .small{font-size:13px;color:#6b7280}
</style>
</head>
<body>
  <div class="card" role="main" aria-labelledby="title">
    <div class="header">
      <h1 id="title">{$escTitle}</h1>
    </div>
    <div class="body">
      <p class="message">{$escMsg}</p>
      <div class="actions">
        {$actions}
      </div>
      <p class="note">Si tienes problemas, contacta con el soporte.</p>
    </div>
  </div>
</body>
</html>
HTML;
}
?>