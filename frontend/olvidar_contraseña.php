<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Reestablecer contraseña</title>

    <link rel="stylesheet" href="../styles/olvidar_contraseña.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* mínimo para que .hidden funcione si no está en el CSS */
        .hidden { display: none !important; }
        .inline-code { margin-top: 12px; display:flex; gap:8px; align-items:center; }
        .inline-code input { padding:8px; border-radius:6px; border:1px solid #d7dbe8; }
        .inline-code button { padding:8px 10px; border-radius:6px; background:#2563eb; color:#fff; border:0; cursor:pointer; }
    </style>
</head>
<body>
    <div class="card" role="main">
        <h2>Reestablecer contraseña</h2>

        <!-- Paso 1: ingresar correo -->
        <form id="emailForm">
            <div id="step-email">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" placeholder="tu@correo.com" required />
                <div class="row" style="margin-top:14px">
                    <button id="sendCodeBtn" type="submit">Enviar código</button>
                    <button id="cancelBtn" class="secondary" type="button">Cancelar</button>
                </div>

                <!-- aqui aparecerá el input para poner el código inline -->
                <div id="inlineCodeContainer" class="hidden"></div>

                <div class="message" id="msg-email"></div>
            </div>
        </form>

        <!-- Paso 2: ingresar código (mantengo el bloque por compatibilidad, no se mostrará) -->
        <form id="codeForm" class="hidden">
            <div id="step-code" class="hidden">
                <label for="code">Ingresa el código</label>
                <input id="code_visible" name="code_visible" type="text" placeholder="Código de verificación" maxlength="8" />
                <div class="small" style="margin-top:8px">
                    No recibiste el código?
                    <button id="resendBtn" class="resend" type="button">Reenviar</button>
                    <span id="resendTimer" class="small"></span>
                </div>
                <div class="row" style="margin-top:14px">
                    <button id="verifyCodeBtn" type="submit">Verificar código</button>
                    <button id="backToEmailBtn" class="secondary" type="button">Volver</button>
                </div>
                <div class="message" id="msg-code"></div>
            </div>
        </form>

        <!-- Paso 3: nueva contraseña -->
        <form id="resetForm">
            <div id="step-reset" class="hidden">
                <!-- hidden field to store verified code -->
                <input type="hidden" id="code" name="code" value="">
                <label for="newPassword">Nueva contraseña</label>
                <input id="newPassword" name="password" type="password" placeholder="Nueva contraseña" />
                <label for="confirmPassword">Confirmar contraseña</label>
                <input id="confirmPassword" name="password2" type="password" placeholder="Repite la contraseña" />
                <div class="row" style="margin-top:14px">
                    <button id="resetPasswordBtn" type="submit">Guardar contraseña</button>
                    <button id="cancel2Btn" class="secondary" type="button">Cancelar</button>
                </div>
                <div class="message" id="msg-reset"></div>
            </div>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpointSend = '../backend/forgot_send_code.php';
    const endpointVerify = '../backend/forgot_verify_code.php';
    const endpointReset = '../backend/forgot_reset_password.php';

    let currentEmail = '';

    const show = (sel, msg, color = 'crimson') => {
        const el = document.querySelector(sel);
        if (el) { el.textContent = msg; el.style.color = color; }
    };

    async function postJson(url, data) {
        const resp = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        });
        const text = await resp.text();
        try { return JSON.parse(text); }
        catch (e) { throw new Error('Respuesta no JSON: ' + text); }
    }

    function createInlineCodeInput() {
        const container = document.getElementById('inlineCodeContainer');
        if (!container) return;
        if (!container.classList.contains('hidden')) return; // ya creado/visible

        container.classList.remove('hidden');
        container.innerHTML = `
            <div class="inline-code">
                <input id="inlineCode" name="inlineCode" type="text" maxlength="8" placeholder="Ingresa el código">
                <button id="inlineVerifyBtn" type="button">Verificar</button>
                <button id="inlineResendBtn" type="button" style="background:#6b7280">Reenviar</button>
            </div>
        `;

        // listener verificar inline
        document.getElementById('inlineVerifyBtn').addEventListener('click', async function () {
            const code = document.getElementById('inlineCode').value.trim();
            if (!code) { show('#msg-email', 'Ingresa el código'); return; }
            show('#msg-email', 'Verificando...', '#2563eb');
            try {
                const r = await postJson(endpointVerify, { email: currentEmail, code });
                if (r && r.success) {
                    // guardar el código verificado en el campo oculto y mostrar reset
                    document.getElementById('code').value = code;
                    document.getElementById('step-reset').classList.remove('hidden');
                    document.getElementById('step-email').style.display = 'none';
                    show('#msg-reset', 'Código válido. Ingresa tu nueva contraseña.', 'green');
                } else {
                    show('#msg-email', (r && r.message) ? r.message : 'Código inválido');
                }
            } catch (err) {
                console.error('verify error', err);
                show('#msg-email', 'Error de comunicación con el servidor');
            }
        });

        // listener reenviar inline
        document.getElementById('inlineResendBtn').addEventListener('click', async function () {
            if (!currentEmail) return;
            show('#msg-email', 'Reenviando...', '#2563eb');
            try {
                const r = await postJson(endpointSend, { email: currentEmail, resend: 1 });
                if (r && r.success) {
                    show('#msg-email', r.message || 'Código reenviado', 'green');
                } else {
                    show('#msg-email', (r && r.message) ? r.message : 'No se pudo reenviar el código');
                }
            } catch (err) {
                console.error('resend error', err);
                show('#msg-email', 'Error de comunicación con el servidor');
            }
        });
    }

    // enviar código
    document.getElementById('emailForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        if (!email) { show('#msg-email', 'Ingresa un correo válido'); return; }
        show('#msg-email', 'Enviando código...', '#2563eb');
        try {
            const r = await postJson(endpointSend, { email });
            if (r && r.success) {
                currentEmail = email;
                show('#msg-email', 'Código enviado. Ingresa el código aquí abajo.', 'green');
                createInlineCodeInput();
            } else {
                show('#msg-email', (r && r.message) ? r.message : 'Error al enviar código');
            }
        } catch (err) {
            console.error('send error', err);
            show('#msg-email', 'Error de comunicación con el servidor');
        }
    });

    // reset contraseña
    document.getElementById('resetForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const pass = document.getElementById('newPassword').value;
        const pass2 = document.getElementById('confirmPassword').value;
        if (!pass || pass.length < 6) { show('#msg-reset', 'Contraseña mínima 6 caracteres'); return; }
        if (pass !== pass2) { show('#msg-reset', 'Las contraseñas no coinciden'); return; }
        show('#msg-reset', 'Guardando...', '#2563eb');
        const code = document.getElementById('code').value.trim();
        try {
            const r = await postJson(endpointReset, { email: currentEmail, code, password: pass });
            if (r && r.success) {
                show('#msg-reset', r.message || 'Contraseña actualizada', 'green');
                setTimeout(() => window.location.href = 'inicio_de_sesion.html', 2000);
            } else {
                show('#msg-reset', (r && r.message) ? r.message : 'No se pudo cambiar la contraseña');
            }
        } catch (err) {
            console.error('reset error', err);
            show('#msg-reset', 'Error de comunicación con el servidor');
        }
    });

    // botones simples
    document.getElementById('cancelBtn').addEventListener('click', function () { location.href = 'inicio_de_sesion.html'; });
    document.getElementById('cancel2Btn').addEventListener('click', function () { location.href = 'inicio_de_sesion.html'; });
});
</script>
</body>
</html>