<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body{font-family:Inter,system-ui,Arial;background:#f4f7fb;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
        .card{width:100%;max-width:420px;background:#fff;padding:22px;border-radius:12px;box-shadow:0 10px 30px rgba(14,20,40,0.06)}
        h2{margin:0 0 12px;font-size:20px}
        label{display:block;margin-top:12px;font-size:13px;color:#222}
        input{width:100%;padding:10px;border:1px solid #e6eaf0;border-radius:8px;margin-top:6px;font-size:14px}
        .row{display:flex;gap:8px}
        .btn{margin-top:14px;padding:10px 14px;border-radius:9px;border:0;background:linear-gradient(90deg,#4f46e5,#06b6d4);color:#fff;font-weight:600;cursor:pointer}
        .btn[disabled]{opacity:.6;cursor:default}
        .msg{margin-top:12px;padding:10px;border-radius:8px;display:none}
        .msg.success{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
        .msg.error{background:#fff1f2;color:#7f1d1d;border:1px solid #fecaca}
        small{display:block;margin-top:8px;color:#6b7280}
    </style>
</head>
<body>
    <main class="card" role="main" aria-live="polite">
        <h2>Crear cuenta</h2>

        <form id="registroForm" autocomplete="off">
            <label for="usuario">Usuario</label>
            <input id="usuario" name="usuario" type="text" maxlength="50" required />

            <label for="email">Correo electrónico</label>
            <input id="email" name="email" type="email" maxlength="120" required />

            <label for="contrasena">Contraseña</label>
            <input id="contrasena" name="contrasena" type="password" minlength="8" required />

            <label for="contrasena2">Confirmar contraseña</label>
            <input id="contrasena2" name="contrasena2" type="password" minlength="8" required />

            <button type="submit" id="btnSubmit" class="btn">Registrarme</button>

            <div id="successMsg" class="msg success" role="status"></div>
            <div id="errorMsg" class="msg error" role="alert"></div>

            <small>Al registrarte aceptas las condiciones. La contraseña debe tener al menos 8 caracteres.</small>
        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    (function($){
        $('#registroForm').on('submit', function(e){
            e.preventDefault();
            $('#successMsg, #errorMsg').hide().text('');
            const $btn = $('#btnSubmit');
            const usuario = $.trim($('#usuario').val());
            const email = $.trim($('#email').val());
            const pass = $('#contrasena').val();
            const pass2 = $('#contrasena2').val();

            if (!usuario || !email || !pass) {
                $('#errorMsg').text('Complete todos los campos.').show();
                return;
            }
            // validación simple del cliente
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(email)) {
                $('#errorMsg').text('Email inválido.').show();
                return;
            }
            if (pass.length < 8) {
                $('#errorMsg').text('La contraseña debe tener al menos 8 caracteres.').show();
                return;
            }
            if (pass !== pass2) {
                $('#errorMsg').text('Las contraseñas no coinciden.').show();
                return;
            }

            $btn.prop('disabled', true).text('Registrando...');

            $.ajax({
                url: '../backend/registro.php',
                method: 'POST',
                data: $(this).serialize(), // serializar datos del formulario (application/x-www-form-urlencoded)
                dataType: 'json',
                timeout: 15000
            }).done(function(resp){
                if (resp && resp.success) {
                    $('#successMsg').text(resp.message || 'Registrado correctamente. Revisa tu correo.').show();
                    $('#registroForm')[0].reset();
                    // opcional: redirigir después de 2s
                    setTimeout(function(){ window.location.href = 'login.php'; }, 2000);
                } else {
                    $('#errorMsg').text(resp && resp.message ? resp.message : 'Error al registrar.').show();
                }
            }).fail(function(jqXHR, textStatus, err){
                let msg = 'Error de conexión. Intenta nuevamente.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) msg = jqXHR.responseJSON.message;
                $('#errorMsg').text(msg).show();
            }).always(function(){
                $btn.prop('disabled', false).text('Registrarme');
            });
        });
    })(jQuery);
    </script>
</body>
</html>